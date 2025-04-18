<?php
/**
 * Message module for private messages and forward to email
 * 
 * @package    Message
 * @version    2.5.0
 * @author     Nuno Luciano aka gigamaster, 2020 XCL23
 * @author     Osamu Utsugi aka Marijuana
 * @copyright  (c) 2005-2025 The XOOPSCube Project, Authors
 * @license    GPL 3.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
function smarty_function_message_suggestlist($params, &$smarty)
{
    $name = isset($params['name']) ? trim($params['name']) : 'uname';
    $size = isset($params['size']) ? (int)$params['size'] : 30;
    $username = isset($params['uname']) ? trim($params['uname']) : '';
    $id = isset($params['id']) ? trim($params['id']) : 'user-autocomplete';
    $placeholder = isset($params['placeholder']) ? trim($params['placeholder']) : 'Type a username';
  
    $root = XCube_Root::getSingleton();
    $db = $root->mController->getDB();
  
    // Get users data
    $users = [];
    $sql = 'SELECT `uname` FROM `' . $db->prefix('users') . '` ';
    $sql.= 'WHERE `uid` <> ' . $root->mContext->mXoopsUser->get('uid') . ' ';
    $sql.= 'ORDER BY `uname`';
    $result = $db->query($sql);
    while ([$uname] = $db->fetchRow($result)) {
        $users[] = htmlspecialchars($uname, ENT_QUOTES);
    }
    
    // JSON encode the users array for JavaScript
    $usersJson = json_encode($users);
    
    // Create the HTML structure
    $html = '<div class="autocomplete-container">';
    $html .= '<input id="'.$id.'" type="text" name="'.$name.'" value="'.htmlspecialchars($username, ENT_QUOTES).'" 
              autocomplete="off" size="'.$size.'" placeholder="'.$placeholder.'" class="user-input">';
    $html .= '<div id="'.$id.'-results" class="autocomplete-results"></div>';
    $html .= '</div>';
    
    // Add CSS for styling
    $html .= '<style>
        .autocomplete-container {
            position: relative;
            width: 100%;
            max-width: '.($size * 10).'px;
        }
        .user-input {
            width: 100%;
            padding: 8px;
            border: 1px solid red;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .autocomplete-results {
            position: absolute;
            border: 1px solid #212121;
            border-top: none;
            z-index: 99;
            top: 100%;
            left: 0;
            right: 0;
            max-height: 200px;
            overflow-y: auto;
            background-color: #000;
            display: none;
        }
        .autocomplete-item {
            padding: 8px;
            cursor: pointer;
        }
        .autocomplete-item:hover {
            background-color: #212121;
        }
        .autocomplete-active {
            background-color: #272727;
        }
    </style>';
    
    // Add JavaScript for autocomplete functionality
    $html .= '<script>
    document.addEventListener("DOMContentLoaded", function() {
        const users = '.$usersJson.';
        const inputElement = document.getElementById("'.$id.'");
        const resultsElement = document.getElementById("'.$id.'-results");
        let currentFocus = -1;
        
        // Function to display matching results
        function showResults(value) {
            // Clear previous results
            resultsElement.innerHTML = "";
            resultsElement.style.display = "none";
            
            if (!value) return false;
            
            const matchingUsers = users.filter(user => 
                user.toLowerCase().indexOf(value.toLowerCase()) > -1
            );
            
            if (matchingUsers.length > 0) {
                resultsElement.style.display = "block";
                
                matchingUsers.forEach(user => {
                    const item = document.createElement("div");
                    item.className = "autocomplete-item";
                    
                    // Highlight the matching part
                    const matchIndex = user.toLowerCase().indexOf(value.toLowerCase());
                    const beforeMatch = user.substring(0, matchIndex);
                    const match = user.substring(matchIndex, matchIndex + value.length);
                    const afterMatch = user.substring(matchIndex + value.length);
                    
                    item.innerHTML = beforeMatch + "<strong>" + match + "</strong>" + afterMatch;
                    
                    // Set click event
                    item.addEventListener("click", function() {
                        inputElement.value = user;
                        resultsElement.style.display = "none";
                    });
                    
                    resultsElement.appendChild(item);
                });
            }
        }
        
        // Input event listener
        inputElement.addEventListener("input", function() {
            showResults(this.value);
        });
        
        // Focus event listener
        inputElement.addEventListener("focus", function() {
            if (this.value) showResults(this.value);
        });
        
        // Blur event listener - delay hiding to allow for clicks
        inputElement.addEventListener("blur", function() {
            setTimeout(function() {
                resultsElement.style.display = "none";
            }, 200);
        });
        
        // Key navigation
        inputElement.addEventListener("keydown", function(e) {
            const items = resultsElement.getElementsByClassName("autocomplete-item");
            
            if (e.keyCode === 40) { // Down arrow
                currentFocus++;
                setActive(items);
                e.preventDefault();
            } else if (e.keyCode === 38) { // Up arrow
                currentFocus--;
                setActive(items);
                e.preventDefault();
            } else if (e.keyCode === 13) { // Enter
                e.preventDefault();
                if (currentFocus > -1 && items[currentFocus]) {
                    items[currentFocus].click();
                }
            }
        });
        
        // Set active item
        function setActive(items) {
            if (!items.length) return;
            
            // Remove active class from all items
            for (let i = 0; i < items.length; i++) {
                items[i].classList.remove("autocomplete-active");
            }
            
            // Adjust currentFocus if out of bounds
            if (currentFocus >= items.length) currentFocus = 0;
            if (currentFocus < 0) currentFocus = items.length - 1;
            
            // Add active class to current item
            items[currentFocus].classList.add("autocomplete-active");
            
            // Scroll to active item if needed
            items[currentFocus].scrollIntoView({ block: "nearest" });
        }
    });
    </script>';
    
    echo $html;
}
