<?php
/**
 * EnPharChem - Offline License Signer (VENDOR-SIDE CLI)
 * ----------------------------------------------------
 * Generates a signed offline license file that on-prem installs verify with
 * lib/OfflineLicense.php. Run this only on the vendor's machine that holds the
 * PRIVATE key — never ship the private key to customers.
 *
 * Generate a key pair once:
 *   openssl genrsa -out vendor_private.pem 2048
 *   openssl rsa -in vendor_private.pem -pubout -out vendor_public.pem
 *   (ship vendor_public.pem to deploy/keys/ in the image; keep the private key safe)
 *
 * Usage:
 *   php lib/sign-license.php \
 *       --key=vendor_private.pem \
 *       --customer="Acme Refining" \
 *       --expiry=2027-12-31 \
 *       --seats=25 \
 *       --modules=flowsheet-simulator,process-sim-chemicals \
 *       --out=license.lic
 *
 *   # grant everything:
 *   php lib/sign-license.php --key=vendor_private.pem --customer="Acme" --modules=* --out=license.lic
 */

$args = [];
foreach (array_slice($argv, 1) as $a) {
    if (preg_match('/^--([^=]+)=(.*)$/s', $a, $m)) $args[$m[1]] = $m[2];
}

$keyFile = $args['key'] ?? '';
if (!$keyFile || !is_file($keyFile)) {
    fwrite(STDERR, "ERROR: --key=<vendor_private.pem> is required.\n");
    exit(1);
}
$priv = openssl_pkey_get_private(file_get_contents($keyFile));
if ($priv === false) {
    fwrite(STDERR, "ERROR: could not load private key.\n");
    exit(1);
}

$modulesArg = $args['modules'] ?? '*';
$modules = ($modulesArg === '*') ? '*' : array_values(array_filter(array_map('trim', explode(',', $modulesArg))));

$payload = [
    'customer' => $args['customer'] ?? 'Unnamed',
    'issued'   => date('Y-m-d'),
    'expiry'   => $args['expiry'] ?? null,
    'seats'    => isset($args['seats']) ? (int) $args['seats'] : 1,
    'modules'  => $modules,
];

$license = base64_encode(json_encode($payload, JSON_UNESCAPED_SLASHES));
$sig = '';
openssl_sign($license, $sig, $priv, OPENSSL_ALGO_SHA256);

$doc = ['license' => $license, 'signature' => base64_encode($sig)];
$json = json_encode($doc, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

$out = $args['out'] ?? 'license.lic';
file_put_contents($out, $json);

echo "Signed license written to {$out}\n";
echo "  customer: {$payload['customer']}\n";
echo "  expiry:   " . ($payload['expiry'] ?: 'perpetual') . "\n";
echo "  modules:  " . ($modules === '*' ? '* (all)' : implode(', ', $modules)) . "\n";
