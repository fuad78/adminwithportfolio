<?php
require_once 'config.php';

$pageTitle = "VLSM Calculator - KM Fuad Hasan";
include 'includes/header.php';

// VLSM Calculation Functions
function ipToLong($ip) {
    return ip2long($ip);
}

function longToIp($long) {
    return long2ip($long);
}

function calculateSubnetMask($cidr) {
    $mask = 0xFFFFFFFF << (32 - $cidr);
    return longToIp($mask);
}

function getNetworkAddress($ip, $cidr) {
    $ipLong = ipToLong($ip);
    $maskLong = ipToLong(calculateSubnetMask($cidr));
    $networkLong = $ipLong & $maskLong;
    return longToIp($networkLong);
}

function getBroadcastAddress($ip, $cidr) {
    $ipLong = ipToLong($ip);
    $maskLong = ipToLong(calculateSubnetMask($cidr));
    $networkLong = $ipLong & $maskLong;
    $hostBits = 32 - $cidr;
    $broadcastLong = $networkLong | ((1 << $hostBits) - 1);
    return longToIp($broadcastLong);
}

function getUsableIPs($networkIp, $broadcastIp) {
    $networkLong = ipToLong($networkIp);
    $broadcastLong = ipToLong($broadcastIp);
    $firstUsable = longToIp($networkLong + 1);
    $lastUsable = longToIp($broadcastLong - 1);
    return ['first' => $firstUsable, 'last' => $lastUsable];
}

function calculateRequiredHosts($hosts) {
    // Add 2 for network and broadcast addresses
    $required = $hosts + 2;
    // Find the smallest power of 2 that can accommodate the required hosts
    $bits = ceil(log($required, 2));
    return pow(2, $bits);
}

function calculateCIDR($hosts) {
    $required = $hosts + 2;
    $bits = ceil(log($required, 2));
    return 32 - $bits;
}

$results = [];
$baseNetwork = '';
$baseCIDR = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $baseNetwork = $_POST['base_network'] ?? '';
    $baseCIDR = (int)($_POST['base_cidr'] ?? 24);
    
    $departments = [];
    $i = 0;
    while (isset($_POST["dept_name_$i"]) && isset($_POST["dept_hosts_$i"])) {
        $name = trim($_POST["dept_name_$i"]);
        $hosts = (int)$_POST["dept_hosts_$i"];
        if ($name && $hosts > 0) {
            $departments[] = ['name' => $name, 'hosts' => $hosts];
        }
        $i++;
    }
    
    // Sort departments by hosts (descending) for VLSM
    usort($departments, function($a, $b) {
        return $b['hosts'] - $a['hosts'];
    });
    
    if ($baseNetwork && !empty($departments)) {
        $currentIp = ipToLong($baseNetwork);
        $baseMask = ipToLong(calculateSubnetMask($baseCIDR));
        $baseNetworkLong = $currentIp & $baseMask;
        
        foreach ($departments as $dept) {
            $requiredHosts = calculateRequiredHosts($dept['hosts']);
            $cidr = calculateCIDR($dept['hosts']);
            $subnetMask = calculateSubnetMask($cidr);
            $networkAddress = longToIp($currentIp);
            $broadcastAddress = getBroadcastAddress($networkAddress, $cidr);
            $usableIPs = getUsableIPs($networkAddress, $broadcastAddress);
            $totalIPs = ipToLong($broadcastAddress) - ipToLong($networkAddress) + 1;
            $usableCount = $totalIPs - 2;
            
            $results[] = [
                'department' => $dept['name'],
                'hosts' => $dept['hosts'],
                'network' => $networkAddress,
                'subnet_mask' => $subnetMask,
                'cidr' => $cidr,
                'broadcast' => $broadcastAddress,
                'first_usable' => $usableIPs['first'],
                'last_usable' => $usableIPs['last'],
                'usable_count' => $usableCount,
                'total_ips' => $totalIPs
            ];
            
            // Move to next subnet
            $currentIp = ipToLong($broadcastAddress) + 1;
        }
    }
}
?>

<main class="pt-24 pb-16 min-h-screen relative overflow-hidden flex flex-col justify-center">
    <!-- Extra Ambient Glows -->
    <div class="absolute top-1/4 left-1/3 w-80 h-80 bg-indigo-500/5 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/3 w-96 h-96 bg-purple-500/5 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
        <!-- Page Title -->
        <div class="text-center mb-10">
            <h1 class="font-display text-4xl md:text-5xl font-extrabold tracking-tight bg-gradient-to-r from-indigo-400 via-purple-400 to-fuchsia-400 bg-clip-text text-transparent mb-3">
                VLSM Subnet Calculator
            </h1>
            <div class="w-12 h-1 bg-gradient-to-r from-indigo-500 to-purple-500 mx-auto rounded-full mb-4"></div>
            <p class="text-slate-300 font-display font-medium text-lg max-w-xl mx-auto">
                Calculate Variable Length Subnet Masks and visualize network topology
            </p>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left Side: Animated Network Design -->
            <div class="glass-card rounded-2xl p-6 border border-slate-800/80 shadow-2xl flex flex-col justify-between">
                <h2 class="font-display text-lg font-bold text-slate-200 mb-4">Network Topology Map</h2>
                
                <div class="relative h-96 overflow-hidden bg-slate-950/40 rounded-xl border border-slate-900" id="networkAnimation">
                    <svg class="w-full h-full" viewBox="0 0 800 600" xmlns="http://www.w3.org/2000/svg">
                        <!-- Background Grid -->
                        <defs>
                            <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                                <path d="M 40 0 L 0 0 0 40" fill="none" stroke="#334155" stroke-width="0.5" opacity="0.2"/>
                            </pattern>
                            <radialGradient id="routerGradient" cx="50%" cy="50%">
                                <stop offset="0%" style="stop-color:#4f46e5;stop-opacity:1" />
                                <stop offset="100%" style="stop-color:#312e81;stop-opacity:1" />
                            </radialGradient>
                            <radialGradient id="nodeGradient" cx="50%" cy="50%">
                                <stop offset="0%" style="stop-color:#c084fc;stop-opacity:1" />
                                <stop offset="100%" style="stop-color:#581c87;stop-opacity:1" />
                            </radialGradient>
                        </defs>
                        <rect width="800" height="600" fill="url(#grid)"/>
                        
                        <!-- Router Node -->
                        <g id="router">
                            <rect x="350" y="250" width="100" height="60" rx="10" fill="url(#routerGradient)" opacity="0.9" class="router-box"/>
                            <text x="400" y="285" text-anchor="middle" fill="#f8fafc" font-family="Outfit" font-size="14" font-weight="bold">Gateway</text>
                            <!-- Status LEDs -->
                            <circle cx="360" cy="270" r="4" fill="#34d399">
                                <animate attributeName="opacity" values="1;0.3;1" dur="1.5s" repeatCount="indefinite"/>
                            </circle>
                            <circle cx="360" cy="290" r="4" fill="#34d399">
                                <animate attributeName="opacity" values="0.3;1;0.3" dur="1.5s" repeatCount="indefinite"/>
                            </circle>
                            <circle cx="440" cy="270" r="4" fill="#34d399">
                                <animate attributeName="opacity" values="1;0.3;1" dur="1.5s" begin="0.5s" repeatCount="indefinite"/>
                            </circle>
                            <circle cx="440" cy="290" r="4" fill="#34d399">
                                <animate attributeName="opacity" values="0.3;1;0.3" dur="1.5s" begin="0.5s" repeatCount="indefinite"/>
                            </circle>
                        </g>
                        
                        <!-- Departments/Subnets -->
                        <?php if (!empty($results)): ?>
                            <?php 
                            $angles = [];
                            $count = count($results);
                            for ($i = 0; $i < $count; $i++) {
                                $angles[] = (2 * M_PI * $i) / $count;
                            }
                            ?>
                            <?php foreach ($results as $index => $result): ?>
                                <?php 
                                $angle = $angles[$index];
                                $radius = 200;
                                $centerX = 400;
                                $centerY = 280;
                                $x = $centerX + $radius * cos($angle);
                                $y = $centerY + $radius * sin($angle);
                                ?>
                                <!-- Connection Line -->
                                <line x1="<?php echo $centerX; ?>" y1="<?php echo $centerY; ?>" 
                                      x2="<?php echo $x; ?>" y2="<?php echo $y; ?>" 
                                      stroke="#818cf8" stroke-width="2.5" opacity="0.6" 
                                      stroke-dasharray="8,4" class="network-line">
                                    <animate attributeName="stroke-dashoffset" values="0;-12" dur="1.5s" repeatCount="indefinite"/>
                                </line>
                                
                                <!-- Department Node -->
                                <g class="dept-node">
                                    <circle cx="<?php echo $x; ?>" cy="<?php echo $y; ?>" r="45" 
                                            fill="url(#nodeGradient)" opacity="0.7">
                                        <animate attributeName="r" values="45;48;45" dur="2.5s" repeatCount="indefinite"/>
                                    </circle>
                                    <circle cx="<?php echo $x; ?>" cy="<?php echo $y; ?>" r="40" 
                                            fill="url(#nodeGradient)" opacity="0.9"/>
                                    <text x="<?php echo $x; ?>" y="<?php echo $y - 12; ?>" 
                                          text-anchor="middle" fill="#f8fafc" font-family="Outfit" font-size="11" font-weight="bold">
                                        <?php echo htmlspecialchars(substr($result['department'], 0, 12)); ?>
                                    </text>
                                    <text x="<?php echo $x; ?>" y="<?php echo $y + 3; ?>" 
                                          text-anchor="middle" fill="#e2e8f0" font-size="10">
                                        <?php echo $result['hosts']; ?> Hosts
                                    </text>
                                    <text x="<?php echo $x; ?>" y="<?php echo $y + 18; ?>" 
                                          text-anchor="middle" fill="#a5b4fc" font-size="9" font-family="monospace">
                                        /<?php echo $result['cidr']; ?>
                                    </text>
                                    <!-- Node Status Indicator -->
                                    <circle cx="<?php echo $x + 30; ?>" cy="<?php echo $y - 30; ?>" r="5" fill="#34d399">
                                        <animate attributeName="opacity" values="1;0.4;1" dur="1.5s" repeatCount="indefinite"/>
                                    </circle>
                                </g>
                                
                                <!-- Data Flow Animation - Multiple Packets -->
                                <?php for ($p = 0; $p < 3; $p++): ?>
                                <circle r="4" fill="#34d399" opacity="0.8">
                                    <animateMotion dur="2.5s" repeatCount="indefinite" begin="<?php echo ($index * 0.4) + ($p * 0.7); ?>s">
                                        <mpath href="#line-<?php echo $index; ?>"/>
                                    </animateMotion>
                                    <animate attributeName="opacity" values="0;1;1;0" dur="2.5s" repeatCount="indefinite" begin="<?php echo ($index * 0.4) + ($p * 0.7); ?>s"/>
                                </circle>
                                <?php endfor; ?>
                                
                                <!-- Path for data packet animation -->
                                <path id="line-<?php echo $index; ?>" d="M <?php echo $centerX; ?> <?php echo $centerY; ?> L <?php echo $x; ?> <?php echo $y; ?>" fill="none" stroke="none"/>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Default Network Visualization -->
                            <g>
                                <!-- Default Subnets -->
                                <g>
                                    <circle cx="200" cy="200" r="40" fill="url(#nodeGradient)" opacity="0.6">
                                        <animate attributeName="r" values="40;43;40" dur="2.5s" repeatCount="indefinite"/>
                                    </circle>
                                    <text x="200" y="205" text-anchor="middle" fill="#f8fafc" font-family="Outfit" font-size="11" font-weight="bold">Dept A</text>
                                    <circle cx="230" cy="180" r="4" fill="#34d399">
                                        <animate attributeName="opacity" values="1;0.3;1" dur="1.5s" repeatCount="indefinite"/>
                                    </circle>
                                </g>
                                <g>
                                    <circle cx="600" cy="200" r="40" fill="url(#nodeGradient)" opacity="0.6">
                                        <animate attributeName="r" values="40;43;40" dur="2.5s" begin="0.5s" repeatCount="indefinite"/>
                                    </circle>
                                    <text x="600" y="205" text-anchor="middle" fill="#f8fafc" font-family="Outfit" font-size="11" font-weight="bold">Dept B</text>
                                    <circle cx="630" cy="180" r="4" fill="#34d399">
                                        <animate attributeName="opacity" values="1;0.3;1" dur="1.5s" begin="0.5s" repeatCount="indefinite"/>
                                    </circle>
                                </g>
                                <g>
                                    <circle cx="200" cy="400" r="40" fill="url(#nodeGradient)" opacity="0.6">
                                        <animate attributeName="r" values="40;43;40" dur="2.5s" begin="1s" repeatCount="indefinite"/>
                                    </circle>
                                    <text x="200" y="405" text-anchor="middle" fill="#f8fafc" font-family="Outfit" font-size="11" font-weight="bold">Dept C</text>
                                    <circle cx="230" cy="380" r="4" fill="#34d399">
                                        <animate attributeName="opacity" values="1;0.3;1" dur="1.5s" begin="1s" repeatCount="indefinite"/>
                                    </circle>
                                </g>
                                <g>
                                    <circle cx="600" cy="400" r="40" fill="url(#nodeGradient)" opacity="0.6">
                                        <animate attributeName="r" values="40;43;40" dur="2.5s" begin="1.5s" repeatCount="indefinite"/>
                                    </circle>
                                    <text x="600" y="405" text-anchor="middle" fill="#f8fafc" font-family="Outfit" font-size="11" font-weight="bold">Dept D</text>
                                    <circle cx="630" cy="380" r="4" fill="#34d399">
                                        <animate attributeName="opacity" values="1;0.3;1" dur="1.5s" begin="1.5s" repeatCount="indefinite"/>
                                    </circle>
                                </g>
                                
                                <!-- Connection Lines -->
                                <line x1="400" y1="280" x2="200" y2="200" stroke="#818cf8" stroke-width="2.5" opacity="0.4" stroke-dasharray="8,4">
                                    <animate attributeName="stroke-dashoffset" values="0;-12" dur="1.5s" repeatCount="indefinite"/>
                                </line>
                                <line x1="400" y1="280" x2="600" y2="200" stroke="#818cf8" stroke-width="2.5" opacity="0.4" stroke-dasharray="8,4">
                                    <animate attributeName="stroke-dashoffset" values="0;-12" dur="1.5s" begin="0.3s" repeatCount="indefinite"/>
                                </line>
                                <line x1="400" y1="280" x2="200" y2="400" stroke="#818cf8" stroke-width="2.5" opacity="0.4" stroke-dasharray="8,4">
                                    <animate attributeName="stroke-dashoffset" values="0;-12" dur="1.5s" begin="0.6s" repeatCount="indefinite"/>
                                </line>
                                <line x1="400" y1="280" x2="600" y2="400" stroke="#818cf8" stroke-width="2.5" opacity="0.4" stroke-dasharray="8,4">
                                    <animate attributeName="stroke-dashoffset" values="0;-12" dur="1.5s" begin="0.9s" repeatCount="indefinite"/>
                                </line>
                            </g>
                        <?php endif; ?>
                    </svg>
                </div>
            </div>
            
            <!-- Right Side: Calculator Form and Results -->
            <div class="space-y-6">
                <!-- Input Form -->
                <div class="glass-card rounded-2xl p-6 border border-slate-800/80 shadow-2xl">
                    <h2 class="font-display text-lg font-bold text-slate-200 mb-4">Configuration Inputs</h2>
                    
                    <form method="POST" id="vlsmForm" class="space-y-6">
                        <!-- Base IP Address -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Base Network Address &amp; CIDR</label>
                            <div class="flex gap-3">
                                <input type="text" name="base_network" value="<?php echo htmlspecialchars($baseNetwork); ?>" 
                                       placeholder="192.168.1.0" required
                                       class="flex-grow bg-slate-950/85 border border-slate-800 rounded-xl px-4 py-2.5 text-slate-100 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all font-mono text-sm">
                                <span class="text-slate-500 self-center font-bold">/</span>
                                <input type="number" name="base_cidr" value="<?php echo htmlspecialchars($baseCIDR ?: 24); ?>" 
                                       min="1" max="30" required
                                       class="w-20 bg-slate-950/85 border border-slate-800 rounded-xl px-4 py-2.5 text-slate-100 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all font-mono text-sm">
                            </div>
                        </div>
                        
                        <!-- Subnets/Departments -->
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Subnet Requests</label>
                                <button type="button" onclick="addDepartment()" 
                                        class="text-xs bg-indigo-500/10 text-indigo-400 hover:bg-indigo-600 hover:text-white border border-indigo-500/20 px-3 py-1 rounded-lg font-medium transition-colors">
                                    + Add Subnet
                                </button>
                            </div>
                            <div id="departments" class="space-y-2 max-h-48 overflow-y-auto pr-1">
                                <div class="department-row flex gap-2">
                                    <input type="text" name="dept_name_0" placeholder="Department Name (e.g. IT)" required
                                           class="flex-grow bg-slate-950/85 border border-slate-800 rounded-xl px-4 py-2.5 text-slate-100 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-xs">
                                    <input type="number" name="dept_hosts_0" placeholder="Hosts" min="1" required
                                           class="w-24 bg-slate-950/85 border border-slate-800 rounded-xl px-4 py-2.5 text-slate-100 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all font-mono text-xs">
                                    <button type="button" onclick="removeDepartment(this)" 
                                            class="text-rose-500 hover:text-rose-400 px-2 font-bold">
                                        ✕
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" 
                                class="w-full py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-display font-semibold hover:brightness-110 active:scale-[0.98] transition-all duration-300 shadow-lg shadow-indigo-600/10">
                            Calculate Subnets
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Results Block -->
        <?php if (!empty($results)): ?>
        <div class="mt-8 glass-card rounded-2xl p-6 border border-slate-800/80 shadow-2xl overflow-hidden">
            <h2 class="font-display text-lg font-bold text-slate-200 mb-4">Calculation Results Table</h2>
            <div class="overflow-x-auto rounded-xl border border-slate-900 bg-slate-950/40">
                <table class="min-w-full divide-y divide-slate-900 font-mono text-xs text-slate-300">
                    <thead class="bg-slate-900/60 font-display text-slate-400 uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="px-5 py-3.5 text-left font-semibold">Subnet/Dept</th>
                            <th class="px-5 py-3.5 text-left font-semibold">Req. Hosts</th>
                            <th class="px-5 py-3.5 text-left font-semibold">Network Addr</th>
                            <th class="px-5 py-3.5 text-left font-semibold">Subnet Mask</th>
                            <th class="px-5 py-3.5 text-left font-semibold">CIDR</th>
                            <th class="px-5 py-3.5 text-left font-semibold">Broadcast Addr</th>
                            <th class="px-5 py-3.5 text-left font-semibold">Usable Range</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-900 bg-slate-950/20">
                        <?php foreach ($results as $result): ?>
                        <tr class="hover:bg-slate-900/35 transition-colors">
                            <td class="px-5 py-4 font-display font-semibold text-slate-200">
                                <?php echo htmlspecialchars($result['department']); ?>
                            </td>
                            <td class="px-5 py-4">
                                <?php echo $result['hosts']; ?>
                            </td>
                            <td class="px-5 py-4 text-indigo-400">
                                <?php echo $result['network']; ?>
                            </td>
                            <td class="px-5 py-4">
                                <?php echo $result['subnet_mask']; ?>
                            </td>
                            <td class="px-5 py-4 font-bold text-purple-400">
                                /<?php echo $result['cidr']; ?>
                            </td>
                            <td class="px-5 py-4 text-rose-400">
                                <?php echo $result['broadcast']; ?>
                            </td>
                            <td class="px-5 py-4 text-emerald-400">
                                <?php echo $result['first_usable']; ?> &rarr; <?php echo $result['last_usable']; ?>
                                <span class="block text-[10px] text-slate-500 mt-0.5">(<?php echo $result['usable_count']; ?> usable IPs)</span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
</main>

<style>
.router-box {
    filter: drop-shadow(0 4px 10px rgba(79, 70, 229, 0.4));
    animation: routerPulse 2.5s ease-in-out infinite;
}

@keyframes routerPulse {
    0%, 100% {
        filter: drop-shadow(0 4px 6px rgba(79, 70, 229, 0.3));
    }
    50% {
        filter: drop-shadow(0 8px 16px rgba(79, 70, 229, 0.6));
    }
}

.dept-node {
    animation: fadeInScale 0.6s ease-out;
    filter: drop-shadow(0 4px 8px rgba(192, 132, 252, 0.3));
}

@keyframes fadeInScale {
    from {
        opacity: 0;
        transform: scale(0.3);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.network-line {
    filter: drop-shadow(0 2px 4px rgba(129, 140, 248, 0.2));
}
</style>

<script>
let deptCount = 1;

function addDepartment() {
    const container = document.getElementById('departments');
    const row = document.createElement('div');
    row.className = 'department-row flex gap-2';
    row.innerHTML = `
        <input type="text" name="dept_name_${deptCount}" placeholder="Department Name" required
               class="flex-grow bg-slate-950/85 border border-slate-800 rounded-xl px-4 py-2.5 text-slate-100 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-xs">
        <input type="number" name="dept_hosts_${deptCount}" placeholder="Hosts" min="1" required
               class="w-24 bg-slate-950/85 border border-slate-800 rounded-xl px-4 py-2.5 text-slate-100 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all font-mono text-xs">
        <button type="button" onclick="removeDepartment(this)" 
                class="text-rose-500 hover:text-rose-400 px-2 font-bold">
            ✕
        </button>
    `;
    container.appendChild(row);
    deptCount++;
}

function removeDepartment(btn) {
    const rows = document.querySelectorAll('.department-row');
    if (rows.length > 1) {
        btn.closest('.department-row').remove();
    } else {
        alert('You must have at least one department.');
    }
}
</script>

<?php include 'includes/footer.php'; ?>
