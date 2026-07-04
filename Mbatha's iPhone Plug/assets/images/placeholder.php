<?php
// Mbatha's iPhone Plug - Dynamic Vector Image Placeholder Generator
header('Content-Type: image/svg+xml');

$name = isset($_GET['name']) ? trim(urldecode($_GET['name'])) : 'Apple Device';
$type = isset($_GET['type']) ? strtolower(trim($_GET['type'])) : 'iphone';

// Curated luxury gradient color combos
$gradient_start = "#141416";
$gradient_end   = "#2a2a30";

if (strpos(strtolower($name), 'gold') !== false) {
    $gradient_end = "#2a2415";
} else if (strpos(strtolower($name), 'purple') !== false) {
    $gradient_end = "#1e152a";
} else if (strpos(strtolower($name), 'titanium') !== false) {
    $gradient_end = "#282622";
} else if (strpos(strtolower($name), 'green') !== false) {
    $gradient_end = "#152a1a";
} else if (strpos(strtolower($name), 'blue') !== false) {
    $gradient_end = "#15202a";
} else if (strpos(strtolower($name), 'red') !== false) {
    $gradient_end = "#2a1515";
}
?>
<svg xmlns="http://www.w3.org/2000/svg" width="400" height="400" viewBox="0 0 400 400">
  <defs>
    <!-- Dark Luxury Metallic Radial Gradient -->
    <radialGradient id="bgGrad" cx="50%" cy="50%" r="70%">
      <stop offset="0%" stop-color="<?php echo $gradient_end; ?>" />
      <stop offset="100%" stop-color="<?php echo $gradient_start; ?>" />
    </radialGradient>
    
    <!-- Gold Metallic Gradient for Accents -->
    <linearGradient id="goldGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#aa7c11" />
      <stop offset="50%" stop-color="#d4af37" />
      <stop offset="100%" stop-color="#f3cf55" />
    </linearGradient>
  </defs>

  <!-- Background -->
  <rect width="100%" height="100%" fill="url(#bgGrad)" />
  
  <!-- Subtle circular grid design -->
  <circle cx="200" cy="180" r="130" fill="none" stroke="#d4af37" stroke-width="1" stroke-dasharray="3,8" opacity="0.15" />
  <circle cx="200" cy="180" r="100" fill="none" stroke="#ffffff" stroke-width="1" opacity="0.05" />

  <?php if ($type === 'airpods'): ?>
    <!-- AirPods Icon vector -->
    <g transform="translate(140, 110)">
      <!-- Left Pod -->
      <path d="M20,40 C20,10 50,10 50,30 C50,45 35,50 35,60 L35,90 C35,95 30,95 28,90 L28,60 C28,50 20,45 20,40 Z" fill="url(#goldGrad)" opacity="0.8"/>
      <!-- Right Pod -->
      <path d="M100,40 C100,10 70,10 70,30 C70,45 85,50 85,60 L85,90 C85,95 90,95 92,90 L92,60 C92,50 100,45 100,40 Z" fill="url(#goldGrad)" opacity="0.8"/>
      <!-- Case Outline -->
      <rect x="25" y="45" width="70" height="60" rx="20" fill="none" stroke="#ffffff" stroke-width="4" opacity="0.25"/>
    </g>
    
  <?php elseif ($type === 'watch'): ?>
    <!-- Apple Watch Vector -->
    <g transform="translate(150, 100)">
      <!-- Strap -->
      <rect x="28" y="-20" width="44" height="200" rx="10" fill="#1c1c1f" stroke="url(#goldGrad)" stroke-width="1" opacity="0.6"/>
      <!-- Watch Case -->
      <rect x="10" y="15" width="80" height="95" rx="22" fill="#141416" stroke="url(#goldGrad)" stroke-width="4"/>
      <!-- Screen Inner -->
      <rect x="16" y="21" width="68" height="83" rx="16" fill="none" stroke="#ffffff" stroke-width="1" opacity="0.15"/>
      <!-- Watch Dial Face -->
      <circle cx="50" cy="62" r="24" fill="none" stroke="url(#goldGrad)" stroke-width="2"/>
      <line x1="50" y1="62" x2="50" y2="48" stroke="#ffffff" stroke-width="2" stroke-linecap="round"/>
      <line x1="50" y1="62" x2="62" y2="62" stroke="#ffffff" stroke-width="2" stroke-linecap="round"/>
      <!-- Digital Crown -->
      <rect x="90" y="45" width="6" height="18" rx="2" fill="url(#goldGrad)"/>
    </g>

  <?php elseif (strpos($type, 'charger') !== false || strpos($type, 'plug') !== false || strpos($type, 'powerbank') !== false): ?>
    <!-- Charger/Power bank Vector -->
    <g transform="translate(145, 110)">
      <rect x="15" y="15" width="80" height="80" rx="15" fill="none" stroke="url(#goldGrad)" stroke-width="4"/>
      <rect x="25" y="25" width="60" height="60" rx="8" fill="none" stroke="#ffffff" stroke-width="1" opacity="0.15"/>
      <circle cx="55" cy="55" r="16" fill="none" stroke="url(#goldGrad)" stroke-width="2"/>
      <!-- Lightning bolt -->
      <path d="M55,43 L47,56 L54,56 L53,67 L63,54 L56,54 Z" fill="url(#goldGrad)"/>
    </g>

  <?php else: ?>
    <!-- default Phone/iPhone Vector -->
    <g transform="translate(145, 90)">
      <!-- Main Outer Shell -->
      <rect x="10" y="10" width="100" height="200" rx="18" fill="none" stroke="url(#goldGrad)" stroke-width="4" />
      <!-- Inner Bezel -->
      <rect x="15" y="15" width="90" height="190" rx="14" fill="none" stroke="#ffffff" stroke-width="1" opacity="0.15" />
      <!-- Dynamic Island / Notch -->
      <rect x="40" y="22" width="40" height="8" rx="4" fill="url(#goldGrad)" opacity="0.75" />
      <!-- Triple Camera Array Outline (subtle backdrop) -->
      <rect x="22" y="28" width="34" height="34" rx="8" fill="none" stroke="#ffffff" stroke-width="1" opacity="0.08" />
      <!-- Camera Lenses -->
      <circle cx="32" cy="38" r="6" fill="none" stroke="url(#goldGrad)" stroke-width="2" opacity="0.5"/>
      <circle cx="46" cy="46" r="6" fill="none" stroke="url(#goldGrad)" stroke-width="2" opacity="0.5"/>
      <circle cx="32" cy="54" r="6" fill="none" stroke="url(#goldGrad)" stroke-width="2" opacity="0.5"/>
    </g>
  <?php endif; ?>

  <!-- Branding details and Product name text overlay -->
  <text x="200" y="340" fill="#ffffff" font-family="'Outfit', sans-serif" font-size="18" font-weight="600" text-anchor="middle" letter-spacing="0.02em">
    <?php echo $name; ?>
  </text>
  
  <text x="200" y="365" fill="#d4af37" font-family="'Inter', sans-serif" font-size="11" font-weight="600" text-anchor="middle" letter-spacing="0.15em" opacity="0.7">
    PREMIUM PRE-OWNED APPLE
  </text>
</svg>
