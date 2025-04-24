$(document).ready(function() {
    const chatMessages = $('#chat-messages');
    const messageForm = $('#message-form');
    let lastMessageId = 0;
    let isSending = false;

    // Function to show error message
    function showError(message) {
        const errorHtml = `
            <div class="message error">
                <div class="message-content">
                    ${message}
                </div>
            </div>
        `;
        chatMessages.append(errorHtml);
        scrollToBottom();
    }

    // Function to load messages
    function loadMessages() {
        const orderId = $('input[name="order_id"]').val();
        $.ajax({
            url: 'chat/get_messages.php',
            method: 'POST',
            data: { order_id: orderId, last_message_id: lastMessageId },
            success: function(response) {
                let data;
                try {
                    // Check if response is already an object
                    if (typeof response === 'object') {
                        data = response;
                    } else {
                        data = JSON.parse(response);
                    }
                    
                    if (data.error) {
                        switch(data.error) {
                            case 'not_logged_in':
                                showError('You must be logged in to view messages.');
                                break;
                            case 'invalid_order':
                                showError('Invalid order. Please refresh the page.');
                                break;
                            case 'database_error':
                                showError('Error loading messages. Please try again later.');
                                break;
                            default:
                                showError('An error occurred while loading messages.');
                        }
                        return;
                    }
                    
                    if (data.messages) {
                        data.messages.forEach(function(message) {
                            appendMessage(message);
                            lastMessageId = Math.max(lastMessageId, message.message_id);
                        });
                        scrollToBottom();
                    }
                } catch (e) {
                    console.error('Error processing response:', e);
                    showError('Error loading messages. Please try again later.');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                showError('Network error. Please check your connection and try again.');
            }
        });
    }

    // Function to append a message to the chat
    function appendMessage(message) {
        let messageHtml;
        
        // Check if message is a system message by content pattern
        const isSystemMessage = message.message.includes("Order has been marked as completed") || 
                              message.message.includes("Order has been confirmed");
        
        if (isSystemMessage) {
            messageHtml = `
                <div class="message system">
                    <div class="message-content">
                        ${message.message}
                    </div>
                </div>
            `;
        } else {
            const isSent = message.sender_id == $('input[name="sender_id"]').val();
            const messageClass = isSent ? 'sent' : 'received';
            const time = new Date(message.created_at).toLocaleTimeString();
            
            messageHtml = `
                <div class="message ${messageClass}">
                    <div class="message-content">
                        ${message.message}
                        <div class="message-time">${time}</div>
                    </div>
                </div>
            `;
        }
        
        chatMessages.append(messageHtml);
    }

    // Function to scroll to bottom of chat
    function scrollToBottom() {
        chatMessages.scrollTop(chatMessages[0].scrollHeight);
    }

    // Handle message submission
    messageForm.on('submit', function(e) {
        e.preventDefault();
        
        if (isSending) {
            return;
        }
        
        const formData = new FormData(this);
        const messageInput = messageForm.find('textarea');
        const message = messageInput.val().trim();
        
        if (!message) {
            showError('Please enter a message');
            return;
        }
        
        isSending = true;
        const submitButton = messageForm.find('button[type="submit"]');
        submitButton.prop('disabled', true);
        
        $.ajax({
            url: 'chat/send_message.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response === 'success') {
                    messageInput.val('');
                    loadMessages();
                } else {
                    let errorMessage = 'Error sending message. Please try again.';
                    
                    switch(response) {
                        case 'error:not_logged_in':
                            errorMessage = 'You must be logged in to send messages.';
                            break;
                        case 'error:invalid_order':
                            errorMessage = 'Invalid order. Please refresh the page.';
                            break;
                        case 'error:invalid_sender':
                            errorMessage = 'Invalid sender information. Please refresh the page.';
                            break;
                        case 'error:unauthorized':
                            errorMessage = 'You are not authorized to send messages in this chat.';
                            break;
                        case 'error:invalid_sender_type':
                            errorMessage = 'Invalid sender type. Please refresh the page.';
                            break;
                        case 'error:empty_message':
                            errorMessage = 'Message cannot be empty.';
                            break;
                        case 'error:order_not_found':
                            errorMessage = 'Order not found or you do not have access to it.';
                            break;
                        case 'error:database_error':
                            errorMessage = 'A database error occurred. Please try again later.';
                            break;
                    }
                    
                    showError(errorMessage);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                showError('Network error. Please check your connection and try again.');
            },
            complete: function() {
                isSending = false;
                submitButton.prop('disabled', false);
            }
        });
    });

    // Load messages initially
    loadMessages();

    // Poll for new messages every 3 seconds
    setInterval(loadMessages, 3000);

    // Auto-scroll to bottom when new messages arrive
    chatMessages.on('DOMNodeInserted', function() {
        scrollToBottom();
    });
}); 