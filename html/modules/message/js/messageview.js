/**
 * Message Module JavaScript Functions
 *
 * @package    Message
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2005-2025 The XOOPSCube Project
 * @license    GPL 3.0
 */

/**
 * Handle message deletion in list views (inbox/outbox)
 * @param {string} formId - The ID of the form containing messages to delete
 * @param {string} statusId - The ID of the element to display status messages
 * @param {string} itemPrefix - The prefix for message item IDs (inbox-item- or outbox-item-)
 * @param {string} emptyMessage - Message to display when list becomes empty
 */
function setupMessageListDelete(formId, statusId, itemPrefix, emptyMessage) {
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById(formId);
        const statusSpan = document.getElementById(statusId);

        if (form) {
            form.addEventListener('submit', function(event) {
                event.preventDefault(); // Stop normal form submission

                // Get selected message IDs (only enabled and checked checkboxes)
                const selectedIds = Array.from(form.querySelectorAll('input[name="delmsg[]"]:checked:not(:disabled)')).map(cb => cb.value);

                if (selectedIds.length === 0) {
                    if (statusSpan) statusSpan.textContent = MESSAGE_DELETEMSG2 || 'Please select a message to delete.';
                    return; // Don't submit if nothing valid is selected
                }

                // Clear previous status and show deleting message
                if (statusSpan) statusSpan.textContent = 'Deleting...';

                // Create FormData from the form - this automatically includes all fields including the token
                const formData = new FormData(form);
                const searchParams = new URLSearchParams(formData);
                
                // Use fetch with proper content type for form submission
                fetch('index.php?action=deleteall&ajax=1', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: searchParams.toString()
                })
                .then(response => {
                    // First check if the response is ok before trying to parse JSON
                    if (!response.ok) {
                        throw new Error('Server returned ' + response.status + ' ' + response.statusText);
                    }
                    
                    // Try to parse as JSON, but handle text responses too
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    } else {
                        // If not JSON, get text and try to parse it
                        return response.text().then(text => {
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                // If parsing fails, return a simple success object
                                //console.warn('Response is not valid JSON:', text); // Debug: Log the raw response
                                return { success: true, message: 'Operation completed' };
                            }
                        });
                    }
                })
                .then(data => {
                    //console.log('Server response:', data); // Debug: log the full response
                    
                    // Handle both success formats
                    if (data.success === true || data.success === 'true' || 
                        (data.deleted_count && data.deleted_count > 0)) {
                        
                        // Success: Remove deleted items from the page
                        if (data.deleted_ids && Array.isArray(data.deleted_ids)) {
                            data.deleted_ids.forEach(id => {
                                const itemToRemove = document.getElementById(`${itemPrefix}${id}`);
                                if (itemToRemove) {
                                    itemToRemove.remove();
                                }
                            });
                        } else if (selectedIds.length > 0) {
                            // Fallback if server didn't return deleted_ids
                            selectedIds.forEach(id => {
                                const itemToRemove = document.getElementById(`${itemPrefix}${id}`);
                                if (itemToRemove) {
                                    itemToRemove.remove();
                                }
                            });
                        }
                        
                        if (statusSpan) statusSpan.textContent = data.message || 'Deleted successfully.';
                        
                        // Check if list is now empty
                        const remainingItems = form.querySelectorAll('.mail-list-item');
                        if (remainingItems.length === 0) {
                            // Show empty message
                            const emptyMessageElement = document.createElement('p');
                            emptyMessageElement.textContent = emptyMessage;
                            form.parentNode.insertBefore(emptyMessageElement, form);
                            
                            // Disable delete button
                            const deleteButton = form.querySelector('button[type="submit"]');
                            if (deleteButton) {
                                deleteButton.disabled = true;
                            }
                        }
                    } else {
                        // Handle failure
                        // console.error('Deletion failed:', data); // Debug: log the full error response
                        if (statusSpan) statusSpan.textContent = data.error || 'Deletion failed.';
                    }
                })
                .catch(error => {
                    // console.error('Network error:', error); // Debbug: log the network error
                    if (statusSpan) statusSpan.textContent = 'Network error. Please try again.';
                });
            });
        }
    });
}

/**
 * Handle single message deletion in view pages
 * @param {string} formId - The ID of the form to delete the message
 * @param {string} statusId - The ID of the element to display status messages
 * @param {string} redirectUrl - URL to redirect to after successful deletion
 */
function setupSingleMessageDelete(formId, statusId, redirectUrl) {
    document.addEventListener('DOMContentLoaded', function() {
        const deleteForm = document.getElementById(formId);
        const deleteStatus = document.getElementById(statusId);
        
        if (deleteForm) {
            deleteForm.addEventListener('submit', function(event) {
                event.preventDefault();
                
                // Get the message ID and type from the form
                const messageId = deleteForm.querySelector('input[name="inbox"]') ? 
                    deleteForm.querySelector('input[name="inbox"]').value : 
                    deleteForm.querySelector('input[name="outbox"]').value;
                
                const inout = deleteForm.querySelector('input[name="inbox"]') ? 'in' : 'out';
                
                // Show deleting status
                if (deleteStatus) deleteStatus.textContent = 'Deleting...';
                
                // Create FormData from the form
                const formData = new FormData(deleteForm);
                const searchParams = new URLSearchParams(formData);

                // Send AJAX request
                fetch(`index.php?action=delete&inout=${inout}&ajax=1`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: searchParams.toString(),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Success - show message and redirect
                        if (deleteStatus) deleteStatus.textContent = data.message || 'Message deleted successfully.';
                        
                        // Redirect after a short delay
                        setTimeout(function() {
                            window.location.href = redirectUrl;
                        }, 500);
                    } else {
                        // Error
                        if (deleteStatus) deleteStatus.textContent = data.error || 'Error deleting message.';
                        // console.error('Delete error:', data); // Debug: log the full error response
                    }
                })
                .catch(error => {
                    // console.error('Network error:', error); // Debug: log the network error
                    if (deleteStatus) deleteStatus.textContent = 'Network error. Please try again.';
                });
            });
        }
    });
}

/**
 * Load message content via AJAX for inbox/outbox view
 * @param {string} id - Message ID
 * @param {string} param - Parameter name (inbox or outbox)
 * @param {string} inout - Message type (in or out)
 */
function loadMessage(id, param, inout) {
    fetch(`index.php?action=view&${param}=${id}&inout=${inout}&ajax=1`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }
            
            // Common updates for both inbox and outbox
            document.querySelector('.mail-content').innerHTML = data.body;
            
            // Update navigation buttons
            let navHtml = '';
            if (data.prev_id) {
                navHtml += `<a href="#" onclick="loadMessage('${data.prev_id}', '${data.param}', '${data.inout_short}'); return false;" class="outline">&laquo; Previous</a> `;
            }
            if (data.next_id) {
                navHtml += `<a href="#" onclick="loadMessage('${data.next_id}', '${data.param}', '${data.inout_short}'); return false;" class="outline">Next &raquo;</a>`;
            }
            document.getElementById('msg-nav').innerHTML = navHtml;
            
            // Update URL without reloading
            history.pushState({}, '', `index.php?action=view&${param}=${id}&inout=${inout}`);
            
            // Inbox-specific updates
            if (inout === 'in') {
                // Update message title
                const titleElement = document.querySelector('.mail-title');
                if (titleElement) titleElement.innerHTML = data.subject;
                
                // Update author with avatar
                const authorElement = document.querySelector('.mail-author');
                if (authorElement) {
                    authorElement.innerHTML = `From : <img src="${data.avatar_url || XOOPS_URL + '/modules/user/images/no_avatar.gif'}" width="24px" data-self="radius-circle" class="avatar"> ${data.from}`;
                }
                
                document.querySelector('.mail-date').innerHTML = data.date;
                
                // Update form values for delete, lock, and forward forms
                document.querySelectorAll('input[name="inbox"]').forEach(input => {
                    input.value = id;
                });
                
                // Update lock/unlock button based on message status
                const lockForm = document.getElementById('msgLock');
                if (lockForm && data.is_read !== undefined) {
                    const lockInput = lockForm.querySelector('input[name="lock"]');
                    const lockButton = lockForm.querySelector('button[type="submit"]');
                    
                    if (data.is_read == 1) {
                        // Message is read but not locked - show lock button
                        if (lockInput) lockInput.value = "1";
                        if (lockButton) {
                            lockButton.innerHTML = '<img class="svg mail-lock" src="' + XOOPS_URL + '/images/icons/mail-lock.svg" width="1em" height="1em" alt="Mail lock">' +
                                '<span data-self="sm-hide">Lock</span>';
                            lockButton.value = 'Lock';
                        }
                    } else if (data.is_read == 2) {
                        // Message is locked - show unlock button
                        if (lockInput) lockInput.value = "0";
                        if (lockButton) {
                            lockButton.innerHTML = '<img class="svg mail-off" src="' + XOOPS_URL + '/images/icons/mail-off.svg" width="1em" height="1em" alt="Mail off">' +
                                '<span data-self="sm-hide">Unlock</span>';
                            lockButton.value = 'Unlock';
                        }
                    }
                }
                
                // Update delete button state based on message status
                const deleteForm = document.getElementById('msgDelete');
                if (deleteForm && data.is_read !== undefined) {
                    const deleteButton = deleteForm.querySelector('button[type="submit"]');
                    if (deleteButton) {
                        if (data.is_read == 2) {
                            // Locked messages can't be deleted
                            deleteButton.disabled = true;
                            deleteButton.setAttribute('aria-label', 'Cannot delete locked message');
                        } else {
                            // Other messages can be deleted
                            deleteButton.disabled = false;
                            deleteButton.removeAttribute('aria-label');
                        }
                    }
                }
            } 
            // Outbox-specific updates
            else if (inout === 'out') {
                // Update message subject
                const subjectElement = document.querySelector('.mail-subject');
                if (subjectElement) subjectElement.innerHTML = data.subject;
                
                // Update recipient with avatar
                const toElement = document.querySelector('.mail-to');
                if (toElement) {
                    toElement.innerHTML = `To: <img src="${data.avatar_url || XOOPS_URL + '/modules/user/images/no_avatar.gif'}" width="24px" data-self="radius-circle" alt="avatar"> ${data.to}`;
                }
                
                // Update date with icon
                document.querySelector('.mail-date').innerHTML = 
                    '<img class="svg datetime" src="' + XOOPS_URL + '/images/icons/datetime.svg" alt="datetime"> ' + 
                    '<span class="badge">' + data.date + '</span>';
                
                // Update form values for delete form
                document.querySelectorAll('input[name="outbox"]').forEach(input => {
                    input.value = id;
                });
            }
        })
        .catch(error => {
            // console.error('Error loading message:', error); // Debug: log the error
            alert('Error loading message. Please try again.');
        });
}

// Helper function for the forward message form
function submitForm() {
    let form = document.getElementById("forward_message");
    if (form) form.submit();
}