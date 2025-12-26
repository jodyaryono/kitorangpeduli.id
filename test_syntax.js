// Test if there's a syntax error by extracting key patterns from fill.blade.php

// Pattern 1: detectGPS function call
onclick = "detectGPS(123)";

// Pattern 2: resetMapToWilayah
onclick2 = "resetMapToWilayah(123)";

// Pattern 3: Template literal with function
const test = `text ${someFunc(param)} more`;

// Pattern 4: Nested parentheses
function test() {
    showModal({ type: 'success', title: 'Test', message: 'Message' });
}

// Pattern 5: Arrow function in template
const html = `
    <button onclick="handleClick(${123})">Click</button>
`;

console.log('Syntax OK');
