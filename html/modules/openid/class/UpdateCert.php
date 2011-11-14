<?php
/**
 * Update Cert File.
 * @author Original author: Kisara (http://www.kisara-s.com/)
 * @version $Rev$
 * @link $URL$
 */
class UpdateCert
{
    function download_convert_cert($crt, $txt)
    {
        $url = 'http://mxr.mozilla.org/seamonkey/source/security/nss/lib/ckfw/builtins/certdata.txt?raw=1';

        $parsed_url = parse_url($url);

        if (!isset($parsed_url['port'])) {
            $parsed_url['port'] = 80;
        }

        //if (!is_file($txt)) {
            $path_and_query = $parsed_url['path'];
            if ($parsed_url['query'] != '') {
                $path_and_query .= '?' . $parsed_url['query'];
            }

            $request_header  = 'GET ' . $path_and_query . ' HTTP/1.0' . "\x0D\x0A";
            $request_header .= 'Host: ' . $parsed_url['host'] . "\x0D\x0A";
            $request_header .= 'User-Agent: mk-ca-bundle.php' . "\x0D\x0A";

            $fp_sock = fsockopen($parsed_url['host'], $parsed_url['port'], $errno, $errstr, 15);
            if (!$fp_sock) {
                return false;
            }
            fwrite($fp_sock, $request_header."\x0D\x0A");

            $code = fgets($fp_sock, 4096);
            if (!preg_match('/\s(\d{3})\s/', $code, $match) || $match[1] != '200') {
                return false;
            }
            while (!feof($fp_sock)) {
                $line = fgets($fp_sock, 4096);
                if ($line == "\x0D\x0A") {
                    break;
                }
            }
            // Content
            $fpw = fopen($txt, 'wt');
            while (!feof($fp_sock)) {
                fwrite($fpw, fread($fp_sock, 4096));
            }
            fclose($fpw);
            fclose($fp_sock);
        //}

        $fpw = fopen($crt, 'wt');

        $currentdate = date(DATE_RFC822);
        $crt_head = <<<EOT
##
## $crt -- Bundle of CA Root Certificates
##
## Converted at: $currentdate
##
## This is a bundle of X.509 certificates of public Certificate Authorities
## (CA). These were automatically extracted from Mozilla's root certificates
## file (certdata.txt).  This file can be found in the mozilla source tree:
## '/mozilla/security/nss/lib/ckfw/builtins/certdata.txt'
##
## It contains the certificates in PEM format and therefore
## can be directly used with curl / libcurl / php_curl, or with
## an Apache+mod_ssl webserver for SSL client authentication.
## Just configure this file as the SSLCACertificateFile.
##


EOT;
        fputs($fpw, $crt_head);
        $caname = '';
        $fpr = fopen($txt, 'rt');
        while ($line = fgets($fpr)) {
            if (preg_match('/\*\*\*\*\* BEGIN LICENSE BLOCK \*\*\*\*\*/', $line)) {
                fputs($fpw, $line);
                while ($line = fgets($fpr)) {
                    fputs($fpw, $line);
                    if (preg_match('/\*\*\*\*\* END LICENSE BLOCK \*\*\*\*\*/', $line)) {
                        break;
                    }
                }
            }
            if (preg_match('/^#|^\s*$/', $line)) {
                continue;
            }
            $line = rtrim($line);
            if (preg_match('/^CVS_ID\s+\"(.*)\"/', $line, $match)) {
                fputs($fpw, "# $match[1]\n");
            }
            if (preg_match('/^CKA_LABEL\s+[A-Z0-9]+\s+\"(.*)\"/', $line, $match)) {
                $caname = $match[1];
            }
            if (preg_match('/^CKA_VALUE MULTILINE_OCTAL/', $line)) {
                $data = '';
                while ($line = fgets($fpr)) {
                    if (preg_match('/^END/', $line)) {
                        break;
                    }
                    $line = rtrim($line);
                    $octets = explode('\\', $line);
                    array_shift($octets);
                    foreach ($octets as $oct) {
                        $data .= chr(octdec($oct));
                    }
                }
                $pem = "-----BEGIN CERTIFICATE-----\n"
                     . chunk_split(base64_encode($data), 76, "\n")
                     . "-----END CERTIFICATE-----\n";
                fputs($fpw, "\n$caname\n");
                fputs($fpw, str_repeat('=', strlen($caname))."\n");
                fputs($fpw, $pem);
            }
        }
        fclose($fpw);
        fclose($fpr);
        unlink($txt);
        return true;
    }
}
