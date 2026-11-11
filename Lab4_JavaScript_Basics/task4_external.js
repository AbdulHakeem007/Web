/**
 * LAB 4 TASK 4: External JavaScript File
 * 
 * This file demonstrates:
 * 1. Multiple event handlers (click, mouseover, dblclick)
 * 2. Script placement comparison (explained in comments)
 * 3. const object immutability demonstration
 * 4. window.print() functionality
 * 
 * Script Placement Performance Difference:
 * HEAD placement: Script loads before body, blocks rendering, DOM not ready
 * BODY bottom: Script loads after content, faster display, DOM fully ready
 * Best practice: Place scripts at bottom of body or use defer/async attributes
 */

// Wait for DOM to be fully loaded before attaching event listeners
document.addEventListener('DOMContentLoaded', function() {
    console.log('External JS: DOM fully loaded and parsed');

    // ================================
    // EVENT 1: CLICK Handler
    // ================================
    const clickBox = document.getElementById('click-box');
    const clickResult = document.getElementById('click-result');
    let clickCount = 0;

    clickBox.addEventListener('click', function(event) {
        clickCount++;
        const message = `Click #${clickCount} detected! Target: ${event.target.tagName}`;
        clickResult.textContent = message;
        clickResult.style.fontWeight = 'bold';
        log('CLICK EVENT: ' + message);

        // Change background color randomly
        const colors = ['#3498db', '#2980b9', '#1abc9c', '#16a085'];
        this.style.backgroundColor = colors[clickCount % colors.length];
    });

    // ================================
    // EVENT 2: MOUSEOVER / MOUSEOUT
    // ================================
    const hoverBox = document.getElementById('hover-box');
    const hoverResult = document.getElementById('hover-result');
    let hoverCount = 0;

    hoverBox.addEventListener('mouseover', function(event) {
        hoverCount++;
        hoverResult.textContent = `Mouse OVER #${hoverCount}! X:${event.clientX}, Y:${event.clientY}`;
        this.style.transform = 'scale(1.05) rotate(1deg)';
        log('MOUSEOVER EVENT: Mouse entered element');
    });

    hoverBox.addEventListener('mouseout', function() {
        hoverResult.textContent = 'Mouse left the element!';
        this.style.transform = 'scale(1) rotate(0deg)';
        log('MOUSEOUT EVENT: Mouse left element');
    });

    // ================================
    // EVENT 3: DOUBLE-CLICK (dblclick)
    // ================================
    const dblBox = document.getElementById('dbl-box');
    const dblResult = document.getElementById('dbl-result');
    let dblCount = 0;

    dblBox.addEventListener('dblclick', function(event) {
        dblCount++;
        const message = `Double-click #${dblCount}! Time: ${new Date().toLocaleTimeString()}`;
        dblResult.textContent = message;
        dblResult.style.fontSize = '1.2rem';

        // Visual feedback - flash effect
        this.style.backgroundColor = '#c0392b';
        setTimeout(() => {
            this.style.backgroundColor = '#e74c3c';
        }, 300);

        log('DBLCLICK EVENT: ' + message);
    });

    log('External JS loaded: All event listeners attached');
    log('Events registered: click, mouseover, mouseout, dblclick');
});

// ================================
// const OBJECT DEMONSTRATION
// ================================

// Create a constant object
const CONFIG = {
    appName: 'WebEngineeringLab',
    version: '4.0',
    author: 'Hafsa Nayyab',
    settings: {
        theme: 'dark',
        language: 'en'
    }
};

// Function to demonstrate const object behavior
function demonstrateConst() {
    const info = document.getElementById('const-info');

    console.log('Original const CONFIG:', CONFIG);
    info.innerHTML = `
        <strong>const Object Properties:</strong><br>
        App: ${CONFIG.appName}<br>
        Version: ${CONFIG.version}<br>
        Author: ${CONFIG.author}<br>
        Theme: ${CONFIG.settings.theme}<br>
        <em>const prevents reassignment of the variable itself, 
        but properties CAN be modified!</em>
    `;
    log('const object displayed - properties are accessible');
}

// Attempt to modify const object properties (this WORKS!)
function attemptConstModify() {
    const info = document.getElementById('const-info');

    try {
        // This WILL work - modifying properties of const object
        CONFIG.settings.theme = 'light';
        CONFIG.version = '4.1';

        info.innerHTML = `
            <strong>Modified const Object:</strong><br>
            New Theme: ${CONFIG.settings.theme}<br>
            New Version: ${CONFIG.version}<br>
            <span style="color:#f1c40f;">✓ Properties modified successfully!</span><br>
            <em>const only prevents: CONFIG = {} (reassignment)</em><br>
            <em>const allows: CONFIG.property = newValue</em>
        `;
        log('const properties modified - object mutation allowed');

        // Now try to reassign the const itself (this will FAIL)
        try {
            CONFIG = { newProp: 'test' }; // This throws TypeError
        } catch (e) {
            info.innerHTML += `<br><br><span style="color:#e74c3c;">✗ Reassignment blocked: ${e.message}</span>`;
            log('const reassignment blocked: ' + e.message);
        }

    } catch (error) {
        info.innerHTML = `<span style="color:#e74c3c;">Error: ${error.message}</span>`;
        log('ERROR: ' + error.message);
    }
}

// Helper logging function (connected to HTML log box)
function log(message) {
    const logBox = document.getElementById('log-box');
    if (logBox) {
        const p = document.createElement('p');
        p.textContent = '> ' + message;
        logBox.appendChild(p);
        logBox.scrollTop = logBox.scrollHeight;
    }
    console.log(message);
}

// Log when this external file loads
console.log('External JS file (task4_external.js) loaded successfully');
console.log('Script placement: Bottom of body (recommended for performance)');
