<?php
/**
 * EnPharChem - Offline (air-gapped) License Verifier
 * --------------------------------------------------
 * Validates a vendor-signed license file for on-premise installations that
 * cannot reach the online Licensing Portal. This is the EnPharChem analog of
 * a node-locked offline license file.
 *
 * File format (JSON):
 *   {
 *     "license":   "<base64 of the payload JSON>",
 *     "signature": "<base64 RSA-SHA256 signature over the base64 license string>"
 *   }
 *
 * The vendor signs with a private key (see lib/sign-license.php); the install
 * ships only the matching public key (deploy/keys/vendor_public.pem), so a
 * customer cannot forge or alter entitlements.
 *
 * Payload fields:
 *   customer  string
 *   issued    YYYY-MM-DD
 *   expiry    YYYY-MM-DD | null
 *   seats     int (informational)
 *   modules   "*"  | ["flowsheet-simulator", ...]   (by module slug)
 */

class OfflineLicense
{
    /**
     * Verify a license file against the public key.
     * @return array|null decoded payload if valid & unexpired, else null
     */
    public static function verify($file, $pubKeyFile)
    {
        if (!is_file($file) || !is_file($pubKeyFile)) return null;
        $doc = json_decode(@file_get_contents($file), true);
        if (!is_array($doc) || empty($doc['license']) || empty($doc['signature'])) return null;

        $pub = @file_get_contents($pubKeyFile);
        if ($pub === false) return null;
        $key = @openssl_pkey_get_public($pub);
        if ($key === false) return null;

        $ok = openssl_verify($doc['license'], base64_decode($doc['signature']), $key, OPENSSL_ALGO_SHA256);
        if ($ok !== 1) return null;

        $payload = json_decode(base64_decode($doc['license']), true);
        if (!is_array($payload)) return null;

        // expiry check (null = perpetual)
        if (!empty($payload['expiry'])) {
            $exp = strtotime($payload['expiry'] . ' 23:59:59');
            if ($exp !== false && $exp < time()) return null;
        }
        return $payload;
    }

    /** Does a verified payload grant a given module slug? */
    public static function grants($payload, $slug)
    {
        $mods = $payload['modules'] ?? [];
        if ($mods === '*' || (is_array($mods) && in_array('*', $mods, true))) return true;
        return is_array($mods) && in_array($slug, $mods, true);
    }
}
