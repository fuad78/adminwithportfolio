<?php
require_once 'config.php';

$pageTitle = "Password Generator - KM Fuad Hasan";
include 'includes/header.php';
?>

<main class="pt-24 pb-16 min-h-screen flex flex-col justify-center relative overflow-hidden">
    <!-- Extra Ambient Lights specifically for this page -->
    <div class="absolute top-1/4 left-1/3 w-80 h-80 bg-indigo-600/10 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute bottom-1/3 right-1/3 w-96 h-96 bg-fuchsia-600/10 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-4xl mx-auto px-4 w-full relative z-10">
        <!-- Page Title -->
        <div class="text-center mb-8">
            <h1 class="font-display text-4xl md:text-5xl font-extrabold tracking-tight bg-gradient-to-r from-indigo-400 via-purple-400 to-fuchsia-400 bg-clip-text text-transparent mb-3">
                Secure Password Generator
            </h1>
            <p class="text-slate-400 max-w-xl mx-auto text-sm md:text-base">
                Create cryptographically secure passwords locally on your machine. Choose custom parameters to fit your security requirements.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Side: Interactive Generator Panel -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Display Card -->
                <div class="glass-card rounded-2xl p-6 shadow-2xl relative border border-slate-800/80 transition-all duration-500" id="generatorCard">
                    <!-- Glow effect container matching strength -->
                    <div class="absolute -inset-px rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-500 opacity-0 blur transition-all duration-500 pointer-events-none" id="strengthGlow"></div>
                    
                    <div class="relative space-y-6">
                        <!-- Password Display -->
                        <div class="relative bg-slate-950/80 rounded-xl p-4 border border-slate-800 flex items-center justify-between group">
                            <input type="text" id="passwordOutput" readonly 
                                   class="bg-transparent text-lg md:text-xl font-mono text-indigo-300 w-full focus:outline-none select-all select-none" 
                                   placeholder="Click Generate...">
                            
                            <div class="flex items-center space-x-2">
                                <!-- Copy Button -->
                                <button onclick="copyToClipboard()" title="Copy to Clipboard"
                                        class="p-2.5 rounded-lg bg-slate-900 hover:bg-indigo-600 hover:text-white text-slate-400 transition-all duration-300 active:scale-95 border border-slate-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="copyIcon">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path>
                                    </svg>
                                </button>
                                <!-- Regenerate Button -->
                                <button onclick="generatePassword()" title="Generate New"
                                        class="p-2.5 rounded-lg bg-slate-900 hover:bg-purple-600 hover:text-white text-slate-400 transition-all duration-300 active:scale-95 border border-slate-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18.28"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Floating Toast Notice -->
                            <div id="copyToast" class="absolute -top-10 right-4 px-3 py-1 rounded bg-emerald-500 text-xs font-semibold text-slate-950 opacity-0 transform translate-y-2 transition-all duration-300 pointer-events-none">
                                Copied!
                            </div>
                        </div>

                        <!-- Strength Meter -->
                        <div class="space-y-2">
                            <div class="flex justify-between items-center text-xs font-semibold tracking-wide uppercase">
                                <span class="text-slate-400">Security Strength</span>
                                <span id="strengthText" class="text-slate-500">Very Weak</span>
                            </div>
                            <div class="h-2 w-full bg-slate-950 rounded-full overflow-hidden flex space-x-1 p-0.5 border border-slate-900">
                                <div id="strengthBar1" class="h-full w-1/4 rounded-full bg-slate-800 transition-all duration-300"></div>
                                <div id="strengthBar2" class="h-full w-1/4 rounded-full bg-slate-800 transition-all duration-300"></div>
                                <div id="strengthBar3" class="h-full w-1/4 rounded-full bg-slate-800 transition-all duration-300"></div>
                                <div id="strengthBar4" class="h-full w-1/4 rounded-full bg-slate-800 transition-all duration-300"></div>
                            </div>
                        </div>

                        <!-- Parameters Form -->
                        <div class="space-y-4 pt-2">
                            <!-- Length Slider -->
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <label class="text-sm font-medium text-slate-300">Password Length</label>
                                    <span id="lengthValue" class="font-mono text-sm px-2.5 py-0.5 rounded bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 font-bold">16</span>
                                </div>
                                <input type="range" id="lengthSlider" min="6" max="64" value="16" oninput="updateLength(this.value)"
                                       class="w-full h-1.5 bg-slate-950 rounded-lg appearance-none cursor-pointer accent-indigo-500">
                            </div>

                            <!-- Options Checklist -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                                <!-- Uppercase -->
                                <label class="flex items-center space-x-3 bg-slate-950/40 hover:bg-slate-950/80 p-3 rounded-xl border border-slate-900 hover:border-slate-800 cursor-pointer transition-all duration-200">
                                    <input type="checkbox" id="optUpper" checked onchange="generatePassword()"
                                           class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500/40 bg-slate-900 border-slate-800">
                                    <span class="text-sm font-medium text-slate-300">Uppercase Letters (A-Z)</span>
                                </label>
                                <!-- Lowercase -->
                                <label class="flex items-center space-x-3 bg-slate-950/40 hover:bg-slate-950/80 p-3 rounded-xl border border-slate-900 hover:border-slate-800 cursor-pointer transition-all duration-200">
                                    <input type="checkbox" id="optLower" checked onchange="generatePassword()"
                                           class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500/40 bg-slate-900 border-slate-800">
                                    <span class="text-sm font-medium text-slate-300">Lowercase Letters (a-z)</span>
                                </label>
                                <!-- Numbers -->
                                <label class="flex items-center space-x-3 bg-slate-950/40 hover:bg-slate-950/80 p-3 rounded-xl border border-slate-900 hover:border-slate-800 cursor-pointer transition-all duration-200">
                                    <input type="checkbox" id="optNumbers" checked onchange="generatePassword()"
                                           class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500/40 bg-slate-900 border-slate-800">
                                    <span class="text-sm font-medium text-slate-300">Numbers (0-9)</span>
                                </label>
                                <!-- Special Characters -->
                                <label class="flex items-center space-x-3 bg-slate-950/40 hover:bg-slate-950/80 p-3 rounded-xl border border-slate-900 hover:border-slate-800 cursor-pointer transition-all duration-200">
                                    <input type="checkbox" id="optSymbols" checked onchange="generatePassword()"
                                           class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500/40 bg-slate-900 border-slate-800">
                                    <span class="text-sm font-medium text-slate-300">Special Symbols (!@#$...)</span>
                                </label>
                            </div>
                        </div>

                        <!-- Main Action Button -->
                        <button onclick="generatePassword()" 
                                class="w-full py-3.5 bg-gradient-to-r from-indigo-600 via-purple-600 to-fuchsia-600 text-white rounded-xl font-display font-semibold hover:brightness-110 active:scale-[0.98] transition-all duration-300 shadow-lg shadow-indigo-600/20">
                            Generate Security Key
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Side: History / Tips Panel -->
            <div class="space-y-6">
                <!-- History Card -->
                <div class="glass-card rounded-2xl p-6 border border-slate-800/80 space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="font-display text-lg font-bold text-slate-200">Recent Keys</h3>
                        <button onclick="clearHistory()" class="text-xs text-slate-500 hover:text-slate-300 transition-colors">Clear</button>
                    </div>
                    
                    <div id="historyContainer" class="space-y-3 max-h-[220px] overflow-y-auto pr-1">
                        <!-- History items populated via JS -->
                        <p class="text-xs text-slate-500 text-center py-4">No recent passwords generated.</p>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="glass-card rounded-2xl p-6 border border-slate-800/80 space-y-3">
                    <h3 class="font-display text-base font-bold text-slate-200 flex items-center space-x-2">
                        <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <span>Security Advice</span>
                    </h3>
                    <ul class="text-xs text-slate-400 space-y-2 list-disc list-inside">
                        <li>Avoid reusing the same password on multiple websites.</li>
                        <li>Passwords over 16 characters are highly recommended for administrative settings.</li>
                        <li>Include symbols and numbers to increase entropy against dictionary brute-force attacks.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    // Constants for character pools
    const UPPER = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    const LOWER = "abcdefghijklmnopqrstuvwxyz";
    const NUMBERS = "0123456789";
    const SYMBOLS = "!@#$%^&*()_+-=[]{}|;:,.<>?";

    let passwordHistory = JSON.parse(localStorage.getItem('pw_history') || '[]');

    function updateLength(val) {
        document.getElementById('lengthValue').innerText = val;
        generatePassword();
    }

    function generatePassword() {
        const length = parseInt(document.getElementById('lengthSlider').value);
        const includeUpper = document.getElementById('optUpper').checked;
        const includeLower = document.getElementById('optLower').checked;
        const includeNumbers = document.getElementById('optNumbers').checked;
        const includeSymbols = document.getElementById('optSymbols').checked;

        let charPool = "";
        if (includeUpper) charPool += UPPER;
        if (includeLower) charPool += LOWER;
        if (includeNumbers) charPool += NUMBERS;
        if (includeSymbols) charPool += SYMBOLS;

        if (charPool === "") {
            document.getElementById('passwordOutput').value = "Select at least 1 option!";
            updateStrengthMeter(0);
            return;
        }

        // Cryptographically secure generation
        let password = "";
        const randomValues = new Uint32Array(length);
        window.crypto.getRandomValues(randomValues);
        for (let i = 0; i < length; i++) {
            password += charPool[randomValues[i] % charPool.length];
        }

        document.getElementById('passwordOutput').value = password;
        
        // Calculate Entropy & update strength
        const entropy = calculateEntropy(password, charPool.length);
        updateStrengthMeter(entropy, length, includeUpper, includeLower, includeNumbers, includeSymbols);
        
        // Add to history
        addToHistory(password);
    }

    function calculateEntropy(pwd, poolSize) {
        if (!pwd) return 0;
        return pwd.length * Math.log2(poolSize);
    }

    function updateStrengthMeter(entropy, length, upper, lower, num, sym) {
        const bars = [
            document.getElementById('strengthBar1'),
            document.getElementById('strengthBar2'),
            document.getElementById('strengthBar3'),
            document.getElementById('strengthBar4')
        ];
        const label = document.getElementById('strengthText');
        const glow = document.getElementById('strengthGlow');
        const card = document.getElementById('generatorCard');

        // Reset classes
        bars.forEach(b => {
            b.style.backgroundColor = '';
            b.className = b.className.replace(/bg-\S+/, 'bg-slate-800');
        });
        
        glow.className = glow.className.replace(/from-\S+ to-\S+/, '');
        glow.style.opacity = '0';
        
        let strength = 0; // 0 to 4
        let labelText = "Very Weak";
        let colorClass = "bg-red-500";
        let glowGradient = "from-red-500 to-orange-500";

        if (entropy > 0) {
            // Count factors
            let typesCount = (upper ? 1 : 0) + (lower ? 1 : 0) + (num ? 1 : 0) + (sym ? 1 : 0);
            
            if (length >= 16 && typesCount >= 3 && entropy > 75) {
                strength = 4;
                labelText = "DevOps Grade Security";
                colorClass = "bg-cyan-400";
                glowGradient = "from-indigo-500 via-purple-500 to-cyan-400";
            } else if (length >= 12 && typesCount >= 3 && entropy > 55) {
                strength = 3;
                labelText = "Strong Security";
                colorClass = "bg-emerald-500";
                glowGradient = "from-emerald-500 to-indigo-500";
            } else if (length >= 8 && typesCount >= 2 && entropy > 35) {
                strength = 2;
                labelText = "Moderate Security";
                colorClass = "bg-amber-500";
                glowGradient = "from-amber-500 to-yellow-400";
            } else {
                strength = 1;
                labelText = "Weak Security";
                colorClass = "bg-red-500";
                glowGradient = "from-red-500 to-orange-500";
            }
        }

        // Apply visual updates
        label.innerText = labelText;
        
        if (strength > 0) {
            // Set text label classes
            label.className = `text-xs font-bold uppercase tracking-wider ${colorClass.replace('bg-', 'text-')}`;
            // Set strength bars active
            for (let i = 0; i < strength; i++) {
                bars[i].className = bars[i].className.replace('bg-slate-800', colorClass);
            }
            // Set Card outer neon glow
            glow.className += ` ${glowGradient}`;
            glow.style.opacity = strength >= 3 ? '0.15' : '0.08';
        } else {
            label.className = "text-xs font-semibold tracking-wide uppercase text-slate-500";
        }
    }

    function copyToClipboard() {
        const output = document.getElementById('passwordOutput');
        if (!output.value || output.value.includes("Select at least")) return;

        navigator.clipboard.writeText(output.value).then(() => {
            const toast = document.getElementById('copyToast');
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';
            
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(2px)';
            }, 1800);
        });
    }

    function addToHistory(pwd) {
        // Prevent duplicates at the top
        if (passwordHistory.length > 0 && passwordHistory[0] === pwd) return;
        
        passwordHistory.unshift(pwd);
        if (passwordHistory.length > 5) {
            passwordHistory.pop();
        }
        localStorage.setItem('pw_history', JSON.stringify(passwordHistory));
        renderHistory();
    }

    function clearHistory() {
        passwordHistory = [];
        localStorage.removeItem('pw_history');
        renderHistory();
    }

    function renderHistory() {
        const container = document.getElementById('historyContainer');
        if (passwordHistory.length === 0) {
            container.innerHTML = `<p class="text-xs text-slate-500 text-center py-4">No recent passwords generated.</p>`;
            return;
        }

        container.innerHTML = passwordHistory.map((pwd, index) => `
            <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-950/60 border border-slate-900 group/item hover:border-slate-800 transition-colors">
                <span class="font-mono text-xs text-slate-400 truncate max-w-[170px]">${pwd}</span>
                <button onclick="copySpecific('${pwd}')" class="text-xs text-indigo-400 hover:text-indigo-300 opacity-0 group-hover/item:opacity-100 transition-opacity px-2 py-0.5 bg-indigo-500/10 rounded border border-indigo-500/20">
                    Copy
                </button>
            </div>
        `).join('');
    }

    function copySpecific(val) {
        navigator.clipboard.writeText(val).then(() => {
            const toast = document.getElementById('copyToast');
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';
            
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(2px)';
            }, 1800);
        });
    }

    // Generate initial password on page load
    document.addEventListener('DOMContentLoaded', () => {
        generatePassword();
        renderHistory();
    });
</script>

<?php include 'includes/footer.php'; ?>
