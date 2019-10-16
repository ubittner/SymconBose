<?php

/*
 * @module      Bose SoundTouch Discovery
 *
 * @file        module.php
 *
 * @prefix      BST
 *
 * @author      Ulrich Bittner
 * @copyright   (c) 2019
 * @license     CC BY-NC-SA 4.0
 *
 * @version     2.03
 * @build       2005
 * @date        2019-10-15, 18:00
 *
 * @see         https://github.com/ubittner/SymconBose
 *
 * @guids		Library
 *              {F5AAB293-F714-4FD1-ADF9-8F30B22201B7}
 *
 *              Bose SoundTouch Discovery
 *              {B14B682A-A8E1-ED81-9669-0238FCF3A086}
 */

// Declare
declare(strict_types=1);

class BoseSoundTouchDiscovery extends IPSModule
{
    public function Create()
    {
        parent::Create();
    }

    public function ApplyChanges()
    {
        // Wait until IP-Symcon is started
        $this->RegisterMessage(0, IPS_KERNELSTARTED);

        parent::ApplyChanges();

        // Check kernel runlevel
        if (IPS_GetKernelRunlevel() != KR_READY) {
            return;
        }
    }

    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {
        $this->SendDebug('MessageSink', 'SenderID: ' . $SenderID . ', Message: ' . $Message, 0);
        switch ($Message) {
            case IPS_KERNELSTARTED:
                $this->KernelReady();
                break;
        }
    }

    /**
     * Applies changes when the kernel is ready.
     */
    private function KernelReady()
    {
        $this->ApplyChanges();
    }

    /**
     * Gets the configuration form.
     *
     * @return false|string
     */
    public function GetConfigurationForm()
    {
        $form = json_decode(file_get_contents(__DIR__ . '/form.json'), true);
        $devices = $this->DiscoverDevices();
        $values = [];
        if (!empty($devices)) {
            foreach ($devices as $key => $device) {
                $ip = $device['deviceIP'];
                $instanceID = $this->GetDeviceInstance($ip);
                $addValue = ['DeviceIP' => $ip, 'DeviceName' => $device['deviceName'], 'DeviceType' => $device['deviceType'], 'DeviceID' => $device['deviceID'], 'instanceID' => $instanceID];
                $addValue['create'] = [['moduleID' => '{4836EF46-FF79-4D6A-91C9-FE54F1BDF2DB}', 'configuration' => ['DeviceIP' => $ip, 'DeviceName' => $device['deviceName']]]];
                $values[] = $addValue;
            }
        }
        $form['actions'][0]['values'] = $values;
        return json_encode($form);
    }

    /**
     * Discovers the devices.
     *
     * @return array
     */
    public function DiscoverDevices(): array
    {
        $devices = $this->MSearch();
        return $devices;
    }

    /**
     * Gets the instance id of a device according to its ip address.
     *
     * @param string $DeviceIP
     * @return int
     */
    private function GetDeviceInstance(string $DeviceIP): int
    {
        $instances = IPS_GetInstanceListByModuleID('{4836EF46-FF79-4D6A-91C9-FE54F1BDF2DB}');
        foreach ($instances as $instance) {
            if (@IPS_GetProperty($instance, 'DeviceIP') == $DeviceIP) {
                return $instance;
            }
        }
        return 0;
    }

    /**
     * Does a msearch for ssdp on network.
     *
     * @param string $st
     * @param int $mx
     * @param string $man
     * @param int|null $from
     * @param int|null $port
     * @param int $sockTimout
     * @return array
     */
    private function MSearch(string $st = 'upnp:rootdevice', int $mx = 2, string $man = 'ssdp:discover', int $from = null, int $port = null, int $sockTimout = 3): array
    {
        $user_agent = 'MacOSX/10.8.2 UPnP/1.1 PHP-UPnP/0.0.1a';
        // BUILD MESSAGE
        $msg = 'M-SEARCH * HTTP/1.1' . "\r\n";
        $msg .= 'HOST: 239.255.255.250:1900' . "\r\n";
        $msg .= 'MAN: "' . $man . '"' . "\r\n";
        $msg .= 'MX: ' . $mx . "\r\n";
        $msg .= 'ST:' . $st . "\r\n";
        $msg .= 'USER-AGENT: ' . $user_agent . "\r\n";
        $msg .= '' . "\r\n";
        // MULTICAST MESSAGE
        $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        if (!$socket) {
            return [];
        }
        socket_set_option($socket, SOL_SOCKET, SO_BROADCAST, true);
        socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, true);
        // SET TIMEOUT FOR RECIEVE
        socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, ['sec' => $sockTimout, 'usec' => 100000]);
        socket_bind($socket, '0.0.0.0', 0);
        if (@socket_sendto($socket, $msg, strlen($msg), 0, '239.255.255.250', 1900) === false) {
            return [];
        }
        // RECIEVE RESPONSE
        $response = [];
        do {
            $buf = null;
            $bytes = @socket_recvfrom($socket, $buf, 2048, 0, $from, $port);
            if ($bytes === false) {
                break;
            }
            if (!is_null($buf)) {
                $response[] = $this->ParseMSearchResponse($buf);
            }
        } while (!is_null($buf));
        // CLOSE SOCKET
        socket_close($socket);
        $existingDevices = [];
        $i = 0;
        foreach ($response as $device) {
            if (isset($device['usn'])) {
                if (strpos($device['usn'], 'USN:uuid:BO5EBO5E-F00D-F00D-FEED') !== false) {
                    if (isset($device['location'])) {
                        preg_match_all('(.*?8091)', $device['location'], $match);
                        $ip = $match[0][0];
                        $ip = str_replace('http://', '', $ip);
                        $ip = str_replace(':8091', '', $ip);
                        // Get info
                        $deviceName = 'Unknown device name';
                        $deviceType = 'Unknown device type';
                        $deviceID = 'Unknown device id';
                        $xmlData = null;
                        $url = 'http://' . $ip . ':8090/info';
                        $ch = curl_init();
                        curl_setopt_array($ch, [
                            CURLOPT_URL            => $url,
                            CURLOPT_HEADER         => 0,
                            CURLOPT_RETURNTRANSFER => 1,
                            CURLOPT_FAILONERROR    => true,
                            CURLOPT_CONNECTTIMEOUT => 5,
                            CURLOPT_TIMEOUT        => 60]);
                        $response = curl_exec($ch);
                        if (curl_errno($ch)) {
                            $error_msg = curl_error($ch);
                        }
                        if ($response) {
                            $this->SendDebug(__FUNCTION__, $response, 0);
                            $xmlData = new SimpleXMLElement($response);
                        }
                        curl_close($ch);
                        if (isset($error_msg)) {
                            $this->SendDebug(__FUNCTION__, 'An error has occurred: ' . json_encode($error_msg), 0);
                        }
                        if (!is_null($xmlData)) {
                            $infoData = json_decode(json_encode($xmlData), true);
                            // Device ID
                            if (array_key_exists('@attributes', $infoData)) {
                                if (array_key_exists('deviceID', $infoData['@attributes'])) {
                                    $deviceID = (string) $infoData['@attributes']['deviceID'];
                                }
                            }
                            // Device name
                            if (array_key_exists('name', $infoData)) {
                                $deviceName = (string) $infoData['name'];
                            }
                            // Device type
                            if (array_key_exists('type', $infoData)) {
                                $deviceType = (string) $infoData['type'];
                            }
                        }
                        $existingDevices[$i] = ['deviceIP' => $ip, 'deviceName' => $deviceName, 'deviceType' => $deviceType, 'deviceID' => $deviceID];
                        $i++;
                    }
                }
            }
        }
        $this->SendDebug(__FUNCTION__ . ' existing Devices', json_encode($existingDevices), 0);
        return $existingDevices;
    }

    /**
     * Parses the mesearch response.
     *
     * @param $response
     * @return array
     */
    private function ParseMSearchResponse($response): array
    {
        $responseArr = explode("\r\n", $response);
        $parsedResponse = [];
        foreach ($responseArr as $key => $row) {
            if (stripos($row, 'http') === 0) {
                $parsedResponse['http'] = $row;
                $this->SendDebug('Discovered Device http:', json_encode($parsedResponse['http']), 0);
            }
            if (stripos($row, 'cach') === 0) {
                $parsedResponse['cache-control'] = str_ireplace('cache-control: ', '', $row);
                $this->SendDebug('Discovered Device cache-control:', json_encode($parsedResponse['cache-control']), 0);
            }
            if (stripos($row, 'st:') === 0) {
                $parsedResponse['st'] = str_ireplace('st: ', '', $row);
                $this->SendDebug('Discovered Device st:', json_encode($parsedResponse['st']), 0);
            }
            if (stripos($row, 'usn:') === 0) {
                $parsedResponse['usn'] = str_ireplace('usn: ', '', $row);
                $this->SendDebug('Discovered Device usn:', json_encode($parsedResponse['usn']), 0);
            }
            if (stripos($row, 'ext') === 0) {
                $parsedResponse['ext'] = str_ireplace('ext: ', '', $row);
                $this->SendDebug('Discovered Device ext:', json_encode($parsedResponse['ext']), 0);
            }
            if (stripos($row, 'serv') === 0) {
                $parsedResponse['server'] = str_ireplace('server: ', '', $row);
                $this->SendDebug('Discovered Device server:', json_encode($parsedResponse['server']), 0);
            }
            if (stripos($row, 'loca') === 0) {
                $parsedResponse['location'] = str_ireplace('location: ', '', $row);
                $this->SendDebug('Discovered Device location:', json_encode($parsedResponse['location']), 0);
            }
        }
        return $parsedResponse;
    }

    //########## Preparation for Symcon Version 5.3

    /*
    private function DiscoverDevices()
    {
        $ssdpID = $this->GetSSDPInstance();
        if ($ssdpID != 0 && IPS_ObjectExists($ssdpID)) {
            $devices = YC_SearchDevices($ssdpID, "ssdp:all");
            $i = 0;
            foreach ($devices as $key => $device) {
                $usn = $device['USN'];
                if(strpos($usn, 'B05E') !== false) {
                    $existingDevices[$i] = ['ip' => $device['IPv4']];
                    $i++;
                }
            }
        }
    }

    private function GetSSDPInstance(): int
    {
        $id = 0;
        $moduleID = '{FFFFA648-B296-E785-96ED-065F7CEE6F29}';
        $ids = IPS_GetInstanceListByModuleID($moduleID);
        if (array_key_exists(0, $ids)) {
            $id = $ids[0];
        }
        return $id;
    }
     */
}

