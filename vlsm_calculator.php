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

<main class="pt-16 min-h-screen bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-4xl font-bold text-center mb-8 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
            VLSM Subnet Calculator
        </h1>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left Side: Animated Network Design -->
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <h2 class="text-2xl font-semibold mb-6 text-gray-800">Network Topology</h2>
                <div class="relative h-96 overflow-hidden" id="networkAnimation">
                    <svg class="w-full h-full" viewBox="0 0 800 600" xmlns="http://www.w3.org/2000/svg">
                        <!-- Background Grid -->
                        <defs>
                            <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                                <path d="M 40 0 L 0 0 0 40" fill="none" stroke="#E5E7EB" stroke-width="0.5" opacity="0.3"/>
                            </pattern>
                            <radialGradient id="routerGradient" cx="50%" cy="50%">
                                <stop offset="0%" style="stop-color:#3B82F6;stop-opacity:1" />
                                <stop offset="100%" style="stop-color:#1E40AF;stop-opacity:1" />
                            </radialGradient>
                            <radialGradient id="nodeGradient" cx="50%" cy="50%">
                                <stop offset="0%" style="stop-color:#8B5CF6;stop-opacity:1" />
                                <stop offset="100%" style="stop-color:#6D28D9;stop-opacity:1" />
                            </radialGradient>
                        </defs>
                        <rect width="800" height="600" fill="url(#grid)"/>
                        
                        <!-- Router -->
                        <g id="router">
                            <rect x="350" y="250" width="100" height="60" rx="8" fill="url(#routerGradient)" opacity="0.9" class="router-box"/>
                            <text x="400" y="285" text-anchor="middle" fill="white" font-size="14" font-weight="bold">Router</text>
                            <!-- Status LEDs -->
                            <circle cx="360" cy="270" r="4" fill="#10B981">
                                <animate attributeName="opacity" values="1;0.3;1" dur="1.5s" repeatCount="indefinite"/>
                            </circle>
                            <circle cx="360" cy="290" r="4" fill="#10B981">
                                <animate attributeName="opacity" values="0.3;1;0.3" dur="1.5s" repeatCount="indefinite"/>
                            </circle>
                            <circle cx="440" cy="270" r="4" fill="#10B981">
                                <animate attributeName="opacity" values="1;0.3;1" dur="1.5s" begin="0.5s" repeatCount="indefinite"/>
                            </circle>
                            <circle cx="440" cy="290" r="4" fill="#10B981">
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
                                      stroke="#6366F1" stroke-width="3" opacity="0.7" 
                                      stroke-dasharray="8,4" class="network-line">
                                    <animate attributeName="stroke-dashoffset" values="0;-12" dur="1.5s" repeatCount="indefinite"/>
                                </line>
                                
                                <!-- Department Node -->
                                <g class="dept-node">
                                    <circle cx="<?php echo $x; ?>" cy="<?php echo $y; ?>" r="45" 
                                            fill="url(#nodeGradient)" opacity="0.8">
                                        <animate attributeName="r" values="45;48;45" dur="2s" repeatCount="indefinite"/>
                                    </circle>
                                    <circle cx="<?php echo $x; ?>" cy="<?php echo $y; ?>" r="40" 
                                            fill="url(#nodeGradient)" opacity="0.9"/>
                                    <text x="<?php echo $x; ?>" y="<?php echo $y - 12; ?>" 
                                          text-anchor="middle" fill="white" font-size="11" font-weight="bold">
                                        <?php echo htmlspecialchars(substr($result['department'], 0, 12)); ?>
                                    </text>
                                    <text x="<?php echo $x; ?>" y="<?php echo $y + 3; ?>" 
                                          text-anchor="middle" fill="white" font-size="10">
                                        <?php echo $result['hosts']; ?> Hosts
                                    </text>
                                    <text x="<?php echo $x; ?>" y="<?php echo $y + 18; ?>" 
                                          text-anchor="middle" fill="white" font-size="9" font-family="monospace">
                                        /<?php echo $result['cidr']; ?>
                                    </text>
                                    <!-- Node Status Indicator -->
                                    <circle cx="<?php echo $x + 30; ?>" cy="<?php echo $y - 30; ?>" r="5" fill="#10B981">
                                        <animate attributeName="opacity" values="1;0.4;1" dur="1.5s" repeatCount="indefinite"/>
                                    </circle>
                                </g>
                                
                                <!-- Data Flow Animation - Multiple Packets -->
                                <?php for ($p = 0; $p < 3; $p++): ?>
                                <circle r="4" fill="#10B981" opacity="0.8">
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
                                    <circle cx="200" cy="200" r="40" fill="url(#nodeGradient)" opacity="0.7">
                                        <animate attributeName="r" values="40;43;40" dur="2s" repeatCount="indefinite"/>
                                    </circle>
                                    <text x="200" y="205" text-anchor="middle" fill="white" font-size="11" font-weight="bold">Dept 1</text>
                                    <circle cx="230" cy="180" r="4" fill="#10B981">
                                        <animate attributeName="opacity" values="1;0.3;1" dur="1.5s" repeatCount="indefinite"/>
                                    </circle>
                                </g>
                                <g>
                                    <circle cx="600" cy="200" r="40" fill="url(#nodeGradient)" opacity="0.7">
                                        <animate attributeName="r" values="40;43;40" dur="2s" begin="0.5s" repeatCount="indefinite"/>
                                    </circle>
                                    <text x="600" y="205" text-anchor="middle" fill="white" font-size="11" font-weight="bold">Dept 2</text>
                                    <circle cx="630" cy="180" r="4" fill="#10B981">
                                        <animate attributeName="opacity" values="1;0.3;1" dur="1.5s" begin="0.5s" repeatCount="indefinite"/>
                                    </circle>
                                </g>
                                <g>
                                    <circle cx="200" cy="400" r="40" fill="url(#nodeGradient)" opacity="0.7">
                                        <animate attributeName="r" values="40;43;40" dur="2s" begin="1s" repeatCount="indefinite"/>
                                    </circle>
                                    <text x="200" y="405" text-anchor="middle" fill="white" font-size="11" font-weight="bold">Dept 3</text>
                                    <circle cx="230" cy="380" r="4" fill="#10B981">
                                        <animate attributeName="opacity" values="1;0.3;1" dur="1.5s" begin="1s" repeatCount="indefinite"/>
                                    </circle>
                                </g>
                                <g>
                                    <circle cx="600" cy="400" r="40" fill="url(#nodeGradient)" opacity="0.7">
                                        <animate attributeName="r" values="40;43;40" dur="2s" begin="1.5s" repeatCount="indefinite"/>
                                    </circle>
                                    <text x="600" y="405" text-anchor="middle" fill="white" font-size="11" font-weight="bold">Dept 4</text>
                                    <circle cx="630" cy="380" r="4" fill="#10B981">
                                        <animate attributeName="opacity" values="1;0.3;1" dur="1.5s" begin="1.5s" repeatCount="indefinite"/>
                                    </circle>
                                </g>
                                
                                <!-- Connection Lines -->
                                <line x1="400" y1="280" x2="200" y2="200" stroke="#6366F1" stroke-width="3" opacity="0.5" stroke-dasharray="8,4">
                                    <animate attributeName="stroke-dashoffset" values="0;-12" dur="1.5s" repeatCount="indefinite"/>
                                </line>
                                <line x1="400" y1="280" x2="600" y2="200" stroke="#6366F1" stroke-width="3" opacity="0.5" stroke-dasharray="8,4">
                                    <animate attributeName="stroke-dashoffset" values="0;-12" dur="1.5s" begin="0.3s" repeatCount="indefinite"/>
                                </line>
                                <line x1="400" y1="280" x2="200" y2="400" stroke="#6366F1" stroke-width="3" opacity="0.5" stroke-dasharray="8,4">
                                    <animate attributeName="stroke-dashoffset" values="0;-12" dur="1.5s" begin="0.6s" repeatCount="indefinite"/>
                                </line>
                                <line x1="400" y1="280" x2="600" y2="400" stroke="#6366F1" stroke-width="3" opacity="0.5" stroke-dasharray="8,4">
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
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h2 class="text-2xl font-semibold mb-4 text-gray-800">Calculator Input</h2>
                    <form method="POST" id="vlsmForm">
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Base Network Address</label>
                            <div class="flex gap-2">
                                <input type="text" name="base_network" value="<?php echo htmlspecialchars($baseNetwork); ?>" 
                                       placeholder="192.168.1.0" required
                                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <span class="text-gray-500 self-center">/</span>
                                <input type="number" name="base_cidr" value="<?php echo htmlspecialchars($baseCIDR); ?>" 
                                       min="1" max="30" required
                                       class="w-20 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-sm font-medium text-gray-700">Departments</label>
                                <button type="button" onclick="addDepartment()" 
                                        class="text-sm bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                                    + Add Department
                                </button>
                            </div>
                            <div id="departments">
                                <div class="department-row mb-3 flex gap-2">
                                    <input type="text" name="dept_name_0" placeholder="Department Name" required
                                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <input type="number" name="dept_hosts_0" placeholder="Hosts" min="1" required
                                           class="w-32 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <button type="button" onclick="removeDepartment(this)" 
                                            class="text-red-600 hover:text-red-800 px-3">
                                        ✕
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" 
                                class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition-all duration-300">
                            Calculate VLSM
                        </button>
                    </form>
                </div>
                
                <!-- Results Table -->
                <?php if (!empty($results)): ?>
                <div class="bg-white rounded-2xl shadow-xl p-6 overflow-x-auto">
                    <h2 class="text-2xl font-semibold mb-4 text-gray-800">Calculation Results</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gradient-to-r from-blue-600 to-purple-600">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Department</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Hosts</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Network Address</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Subnet Mask</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">CIDR</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Broadcast</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Usable IPs</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($results as $result): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($result['department']); ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                        <?php echo $result['hosts']; ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 font-mono">
                                        <?php echo $result['network']; ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 font-mono">
                                        <?php echo $result['subnet_mask']; ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 font-mono">
                                        /<?php echo $result['cidr']; ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 font-mono">
                                        <?php echo $result['broadcast']; ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 font-mono">
                                        <?php echo $result['first_usable']; ?> - <?php echo $result['last_usable']; ?>
                                        <br>
                                        <span class="text-xs text-gray-500">(<?php echo $result['usable_count']; ?> usable)</span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<style>
.router-box {
    filter: drop-shadow(0 4px 6px rgba(59, 130, 246, 0.3));
    animation: routerPulse 2s ease-in-out infinite;
}

@keyframes routerPulse {
    0%, 100% {
        filter: drop-shadow(0 4px 6px rgba(59, 130, 246, 0.3));
    }
    50% {
        filter: drop-shadow(0 8px 12px rgba(59, 130, 246, 0.5));
    }
}

.dept-node {
    animation: fadeInScale 0.6s ease-out;
    filter: drop-shadow(0 4px 8px rgba(139, 92, 246, 0.4));
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
    filter: drop-shadow(0 2px 4px rgba(99, 102, 241, 0.3));
}
</style>

<script>
let deptCount = 1;

function addDepartment() {
    const container = document.getElementById('departments');
    const row = document.createElement('div');
    row.className = 'department-row mb-3 flex gap-2';
    row.innerHTML = `
        <input type="text" name="dept_name_${deptCount}" placeholder="Department Name" required
               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        <input type="number" name="dept_hosts_${deptCount}" placeholder="Hosts" min="1" required
               class="w-32 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        <button type="button" onclick="removeDepartment(this)" 
                class="text-red-600 hover:text-red-800 px-3">
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

// Animate data packets along network lines
document.addEventListener('DOMContentLoaded', function() {
    const packets = document.querySelectorAll('.data-packet');
    packets.forEach((packet, index) => {
        const parent = packet.parentElement;
        const line = parent.querySelector('.network-line');
        if (line) {
            const x1 = parseFloat(line.getAttribute('x1'));
            const y1 = parseFloat(line.getAttribute('y1'));
            const x2 = parseFloat(line.getAttribute('x2'));
            const y2 = parseFloat(line.getAttribute('y2'));
            const dx = x2 - x1;
            const dy = y2 - y1;
            packet.style.setProperty('--tx', dx + 'px');
            packet.style.setProperty('--ty', dy + 'px');
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
