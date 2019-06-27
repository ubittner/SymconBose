<?php

/*
 * @module      Bose SoundTouch
 *
 * @file        module.php
 *
 * @author      Ulrich Bittner
 * @copyright   (c) 2018
 * @license     CC BY-NC-SA 4.0
 *
 * @version     2.01
 * @build       2002
 * @date:       2019-04-23, 10:00
 *
 * @see         https://github.com/ubittner/SymconBoseSoundTouch
 *
 * @guids       Library
 *              {F5AAB293-F714-4FD1-ADF9-8F30B22201B7}
 *
 *              Module
 *              {4836EF46-FF79-4D6A-91C9-FE54F1BDF2DB}
 *
 * @changelog	2019-06-27, 21:42, fix for update device information with no bass capabilities
 *              2019-04-23, 10:00, added changes for module store
 *              2018-08-10, 20:00, initial module script version 2.00
 *
 */

// Declare
declare(strict_types=1);

// Include
include_once __DIR__ . '/helper/autoload.php';

class BoseSoundTouch extends IPSModule
{
    // Helper
    use soundTouchAPI;

    public function Create()
    {
        // Never delete this line!
        parent::Create();

        // Register properties

        // Device data
        $this->RegisterPropertyString('DeviceName', '');
        $this->RegisterPropertyString('DeviceIP', '');
        $this->RegisterPropertyInteger('Timeout', 2000);
        $this->RegisterPropertyInteger('UpdateInformationInterval', 0);

        // Visualisation
        $this->RegisterPropertyBoolean('ShowZoneControl', true);
        $this->RegisterPropertyBoolean('ShowAudioSources', true);
        $this->RegisterPropertyBoolean('ShowMediaInformation', true);
        $this->RegisterPropertyBoolean('ShowTimer', true);
        $this->RegisterPropertyBoolean('ShowAutomaticPowerOn', true);
        $this->RegisterPropertyBoolean('ShowAutomaticPowerOff', true);

        // Custom presets
        $this->RegisterPropertyString('CustomPresets', '');

        // Media file
        $this->RegisterPropertyInteger('MediaFile', 0);

        // Audio notifications
        $this->RegisterPropertyString('AppKey', 'cEVZOEMrzVWIj9fUbT4y14kGAVN2ctA2');
        $this->RegisterPropertyString('ServiceDescription', 'IP-Symcon');
        $this->RegisterPropertyString('MessageDescription', $this->Translate('Audio notifications'));
        $this->RegisterPropertyString('AudioNotifications', '');

        // Register profiles and variables

        // Device power
        $this->RegisterVariableBoolean('DevicePower', $this->Translate('Device power'), '~Switch', 1);
        $this->EnableAction('DevicePower');
        SetValue($this->GetIDForIdent('DevicePower'), 0);

        // Volume slider
        $volumeSlider = 'BST.' . $this->InstanceID . '.VolumeSlider';
        if (!IPS_VariableProfileExists($volumeSlider)) {
            IPS_CreateVariableProfile($volumeSlider, 1);
        }
        IPS_SetVariableProfileValues($volumeSlider, 0, 100, 1);
        IPS_SetVariableProfileText($volumeSlider, '', '%');
        IPS_SetVariableProfileIcon($volumeSlider, 'Speaker');
        $this->RegisterVariableInteger('VolumeSlider', $this->Translate('Volume'), $volumeSlider, 2);
        $this->EnableAction('VolumeSlider');
        $this->SetValue('VolumeSlider', 10);

        // Bass slider
        $bassSlider = 'BST.' . $this->InstanceID . '.BassSlider';
        if (!IPS_VariableProfileExists($bassSlider)) {
            IPS_CreateVariableProfile($bassSlider, 1);
        }
        IPS_SetVariableProfileValues($bassSlider, -9, 0, 1);
        IPS_SetVariableProfileText($bassSlider, '', '%');
        IPS_SetVariableProfileIcon($bassSlider, 'Music');
        $this->RegisterVariableInteger('BassSlider', 'Bass', $bassSlider, 3);
        $this->EnableAction('BassSlider');
        $this->SetValue('BassSlider', 0);

        // Play mode
        $playMode = 'BST.' . $this->InstanceID . '.PlayMode';
        if (!IPS_VariableProfileExists($playMode)) {
            IPS_CreateVariableProfile($playMode, 1);
        }
        IPS_SetVariableProfileAssociation($playMode, 0, '<<', '', -1);
        IPS_SetVariableProfileAssociation($playMode, 1, 'Pause', '', 0xFF0000);
        IPS_SetVariableProfileAssociation($playMode, 2, $this->Translate('Play'), '', 0x00FF00);
        IPS_SetVariableProfileAssociation($playMode, 3, $this->Translate('Fade out'), '', 0xFFFB00);
        IPS_SetVariableProfileAssociation($playMode, 4, '>>', '', -1);
        IPS_SetVariableProfileIcon($playMode, 'HollowArrowRight');
        $this->RegisterVariableInteger('PlayMode', $this->Translate('Play / Pause'), $playMode, 4);
        $this->EnableAction('PlayMode');
        SetValue($this->GetIDForIdent('PlayMode'), true);

        // Device id
        $this->RegisterVariableString('DeviceID', $this->Translate('Device ID'), '', 5);
        IPS_SetIcon($this->GetIDForIdent('DeviceID'), 'Information');
        //$this->SetValue('DeviceID', '');
        IPS_SetHidden($this->GetIDForIdent('DeviceID'), true);

        // Zone control
        $zoneDevices = 'BST.' . $this->InstanceID . '.ZoneDevices';
        if (!IPS_VariableProfileExists($zoneDevices)) {
            IPS_CreateVariableProfile($zoneDevices, 1);
        }
        IPS_SetVariableProfileIcon($zoneDevices, 'Network');
        $this->RegisterVariableInteger('ZoneDevices', $this->Translate('Zone control'), $zoneDevices, 6);
        $this->EnableAction('ZoneDevices');
        IPS_SetIcon($this->GetIDForIdent('ZoneDevices'), 'Network');
        SetValue($this->GetIDForIdent('ZoneDevices'), 0);

        // Audio sources
        $audioSources = 'BST.' . $this->InstanceID . '.AudioSources';
        if (!IPS_VariableProfileExists($audioSources)) {
            IPS_CreateVariableProfile($audioSources, 1);
        }
        IPS_SetVariableProfileIcon($audioSources, 'Database');
        $this->RegisterVariableInteger('AudioSources', $this->Translate('Audio sources'), $audioSources, 7);
        $this->EnableAction('AudioSources');
        SetValue($this->GetIDForIdent('AudioSources'), 1);

        // Media information
        $this->RegisterVariableString('MediaInformation', $this->Translate('Media information'), '~HTMLBox', 8);
        IPS_SetIcon($this->GetIDForIdent('MediaInformation'), 'Information');

        // Timer hour
        $timerHour = 'BST.' . $this->InstanceID . '.TimerHour';
        if (!IPS_VariableProfileExists($timerHour)) {
            IPS_CreateVariableProfile($timerHour, 1);
        }
        for ($i = 0; $i < 24; $i++) {
            IPS_SetVariableProfileAssociation($timerHour, $i, (string) $i, '', 0x0000FF);
        }
        IPS_SetVariableProfileIcon($timerHour, 'Clock');
        $this->RegisterVariableInteger('TimerHour', $this->Translate('Timer (hour)'), 'BST.' . $this->InstanceID . '.TimerHour', 9);
        $this->EnableAction('TimerHour');
        SetValue($this->GetIDForIdent('TimerHour'), 12);

        // Timer minute
        $timerMinute = 'BST.' . $this->InstanceID . '.TimerMinute';
        if (!IPS_VariableProfileExists($timerMinute)) {
            IPS_CreateVariableProfile($timerMinute, 1);
        }
        for ($i = 0; $i <= 55; $i = $i + 5) {
            IPS_SetVariableProfileAssociation($timerMinute, $i, (string) $i, '', 0x0000FF);
        }
        IPS_SetVariableProfileIcon($timerMinute, 'Clock');
        $this->RegisterVariableInteger('TimerMinute', $this->Translate('Timer (minute)'), 'BST.' . $this->InstanceID . '.TimerMinute', 10);
        $this->EnableAction('TimerMinute');
        SetValue($this->GetIDForIdent('TimerMinute'), 0);

        // Select timer source
        $timerSources = 'BST.' . $this->InstanceID . '.TimerSources';
        if (!IPS_VariableProfileExists($timerSources)) {
            IPS_CreateVariableProfile($timerSources, 1);
        }
        IPS_SetVariableProfileIcon($timerSources, 'Execute');
        $this->RegisterVariableInteger('TimerSources', $this->Translate('Timer sources'), $timerSources, 11);
        $this->EnableAction('TimerSources');
        SetValue($this->GetIDForIdent('TimerSources'), 1);

        // Timer volume slider
        $this->RegisterVariableInteger('TimerVolumeSlider', $this->Translate('Volume'), $volumeSlider, 12);
        $this->EnableAction('TimerVolumeSlider');
        $this->SetValue('TimerVolumeSlider', 10);

        // Automatic power on
        $this->RegisterVariableBoolean('AutomaticPowerOn', $this->Translate('Automatic power on'), '~Switch', 13);
        $this->EnableAction('AutomaticPowerOn');
        SetValue($this->GetIDForIdent('AutomaticPowerOn'), false);

        // Volume fade in
        $this->RegisterVariableBoolean('FadeInVolume', $this->Translate('Fade in volume (15 minutes)'), '~Switch', 14);
        $this->EnableAction('FadeInVolume');
        SetValue($this->GetIDForIdent('FadeInVolume'), false);

        // Next power on
        $this->RegisterVariableString('NextPowerOn', $this->Translate('Next power on time'), '', 15);
        IPS_SetIcon($this->GetIDForIdent('NextPowerOn'), 'Information');

        // Next audio source
        $this->RegisterVariableString('NextAudioSource', $this->Translate('Next audio source'), '', 16);
        IPS_SetIcon($this->GetIDForIdent('NextAudioSource'), 'Information');

        // Sleep timer
        $this->RegisterVariableBoolean('AutomaticPowerOff', $this->Translate('Automatic power off'), '~Switch', 17);
        $this->EnableAction('AutomaticPowerOff');
        SetValue($this->GetIDForIdent('AutomaticPowerOff'), false);

        // Volume fade out
        $this->RegisterVariableBoolean('FadeOutVolume', $this->Translate('Fade out volume (15 minutes)'), '~Switch', 18);
        $this->EnableAction('FadeOutVolume');
        SetValue($this->GetIDForIdent('FadeOutVolume'), false);

        // Night mode
        $this->RegisterVariableBoolean('NightMode', $this->Translate('Night mode (30 minutes)'), '~Switch', 19);
        IPS_SetIcon($this->GetIDForIdent('NightMode'), 'Moon');
        $this->EnableAction('NightMode');
        SetValue($this->GetIDForIdent('NightMode'), false);

        // Next power off
        $this->RegisterVariableString('NextPowerOff', $this->Translate('Next power off time'), '', 20);
        IPS_SetIcon($this->GetIDForIdent('NextPowerOff'), 'Information');

        // Set buffer

        // Automatic power on
        $bufferData = ['preset' => 0, 'volume' => 0, 'fadeIn' => false, 'timestamp' => 0, 'firstRun' => false];
        $this->SetBuffer('AutomaticPowerOn', json_encode($bufferData));

        // Automatic power off
        $bufferData = ['fadeOut' => false, 'timestamp' => 0];
        $this->SetBuffer('AutomaticPowerOff', json_encode($bufferData));

        // Register attributes

        // Bass capabilities
        $this->RegisterAttributeBoolean('BassCapabilities', false);

        // Register timer

        // Update Information
        $this->RegisterTimer('UpdateInformation', 0, 'BST_UpdateInformation($_IPS[\'TARGET\']);');

        // Fade out playback
        $this->RegisterTimer('FadeOutPlayback', 0, 'BST_FadeOutPlayback($_IPS[\'TARGET\']);');

        // Automatic power on
        $this->RegisterTimer('AutomaticPowerOn', 0, 'BST_ExecuteAutomaticPowerOn($_IPS[\'TARGET\']);');

        // Automatic power off
        $this->RegisterTimer('AutomaticPowerOff', 0, 'BST_ExecuteAutomaticPowerOff($_IPS[\'TARGET\']);');
    }

    public function ApplyChanges()
    {
        // Wait until IP-Symcon is started
        $this->RegisterMessage(0, IPS_KERNELSTARTED);

        // Never delete this line!
        parent::ApplyChanges();

        // Check kernel runlevel
        if (IPS_GetKernelRunlevel() != KR_READY) {
            return;
        }

        $this->SetTimerInterval('UpdateInformation', $this->ReadPropertyInteger('UpdateInformationInterval') * 1000);

        if (IPS_GetKernelRunlevel() == KR_READY) {
            $this->ValidateConfiguration();

            $deviceIP = $this->ReadPropertyString('DeviceIP');
            if (!$deviceIP) {
                $presets = 'BST.' . $this->InstanceID . '.AudioSources';
                IPS_SetVariableProfileAssociation($presets, 1, $this->Translate('None'), '', 0x0000FF);
                $memberDevices = 'BST.' . $this->InstanceID . '.ZoneDevices';
                IPS_SetVariableProfileAssociation($memberDevices, 0, $this->Translate('Off'), '', 0xFF0000);
                $presets = 'BST.' . $this->InstanceID . '.TimerSources';
                IPS_SetVariableProfileAssociation($presets, 1, $this->Translate('None'), '', 0x0000FF);
            }

            if (IPS_GetInstance($this->InstanceID)['InstanceStatus'] == 102) {
                $this->SetVisualisation();
                $this->CheckBassCapabilities();
                $this->CreateZoneDevicesProfile();
                $this->CreateAudioSourcesProfile();
            }
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

    public function Destroy()
    {
        // Never delete this line!
        parent::Destroy();

        // Delete Profiles
        $this->DeleteProfiles();
    }

    //#################### Request action

    public function RequestAction($Ident, $Value)
    {
        switch ($Ident) {
            case 'DevicePower':
                $this->TogglePowerSwitch($Value);
                break;
            case 'VolumeSlider':
                $this->SetVolumeSlider($Value);
                break;
            case 'BassSlider':
                $this->SetBassSlider($Value);
                break;
            case 'PlayMode':
                $this->SelectPlayMode($Value);
                break;
            case 'ZoneDevices':
                $this->SelectZoneDevice($Value);
                break;
            case 'AudioSources':
                $this->SelectAudioSource($Value);
                break;
            case 'TimerHour':
                $this->SetValue('TimerHour', $Value);
                break;
            case 'TimerMinute':
                $this->SetValue('TimerMinute', $Value);
                break;
            case 'TimerSources':
                $this->SetValue('TimerSources', $Value);
                break;
            case 'TimerVolume':
                $this->SetValue('TimerVolume', $Value);
                break;
            case 'AutomaticPowerOn':
                $this->ToggleAutomaticPowerOn($Value);
                break;
            case 'FadeInVolume':
                $this->SetValue('FadeInVolume', $Value);
                break;
            case 'AutomaticPowerOff':
                $this->ToggleAutomaticPowerOff($Value);
                break;
            case 'FadeOutVolume':
                $this->SetValue('FadeOutVolume', $Value);
                break;
            case 'NightMode':
                $this->ToggleNightMode($Value);
                break;
        }
    }

    //#################### Protected module functions

    /**
     * Toggles the power switch.
     *
     * @param bool $State
     */
    protected function TogglePowerSwitch(bool $State)
    {
        $powerDevice = false;
        $deviceMode = $this->GetDeviceNowPlaying();
        if ($deviceMode) {
            $source = (string) $deviceMode->attributes()->source;
            if ($source == 'STANDBY' && $State == true) {
                $powerDevice = true;
            }
            if ($source != 'STANDBY' && $State == false) {
                $powerDevice = true;
            }
            if ($powerDevice) {
                $power = $this->PowerDevice();
                if ($power) {
                    $this->SetValue('DevicePower', $State);
                }
            }
        }
    }

    /**
     * Sets the volume slider.
     *
     * @param int $Volume
     */
    protected function SetVolumeSlider(int $Volume)
    {
        $volume = $this->SetDeviceVolume($Volume);
        if ($volume) {
            $this->SetValue('VolumeSlider', $Volume);
        }
    }

    /**
     * Sets the bass slider.
     *
     * @param int $Level
     */
    protected function SetBassSlider(int $Level)
    {
        $bass = $this->SetDeviceBass($Level);
        if ($bass) {
            $this->SetValue('BassSlider', $Level);
        }
    }

    /**
     * Toggles the previous track, pause, play, next track buttons.
     *
     * @param int $Mode
     */
    protected function SelectPlayMode(int $Mode)
    {
        $toggle = false;
        switch ($Mode) {
            case 0:
                $previousTrack = $this->SelectDevicePreviousTrack();
                if ($previousTrack) {
                    $toggle = true;
                }
                break;
            case 1:
                $pause = $this->ToggleDevicePlayPause();
                if ($pause) {
                    $toggle = true;
                    $this->SetTimerInterval('FadeOutPlayback', 0);
                }
                break;
            case 2:
                $play = $this->ToggleDevicePlayPause();
                if ($play) {
                    $toggle = true;
                    $this->SetTimerInterval('FadeOutPlayback', 0);
                }
                break;
            case 3:
                $this->FadeOutPlayback();
                $toggle = true;
                break;
            case 4:
                $nextTrack = $this->SelectDeviceNextTrack();
                if ($nextTrack) {
                    $toggle = true;
                }
                break;
        }
        if ($toggle) {
            // Set switch to selected mode
            $this->SetValue('PlayMode', $Mode);
        }
    }

    /**
     * Selects the device to be a member of this zone.
     *
     * @param int $SlaveDeviceInstance
     */
    protected function SelectZoneDevice(int $SlaveDeviceInstance)
    {
        $select = false;
        if ($SlaveDeviceInstance != 0) {
            $masterID = $this->GetValue('DeviceID');
            $memberIP = IPS_GetProperty($SlaveDeviceInstance, 'DeviceIP');
            $memberID = GetValue(IPS_GetObjectIDByIdent('DeviceID', $SlaveDeviceInstance));
            $addDevice = $this->SetDeviceZone($masterID, $memberIP, $memberID);
            if ($addDevice) {
                $select = true;
            }
        } else {
            $getZone = $this->GetDeviceZone();
            if (@isset($getZone->member->attributes()->ipaddress)) {
                $masterID = $this->GetValue('DeviceID');
                $memberID = (string) $getZone->member;
                $memberIP = (string) $getZone->member->attributes()->ipaddress;
                $removeDevice = $this->RemoveDeviceZoneSlave($masterID, $memberIP, $memberID);
                if ($removeDevice) {
                    $select = true;
                }
            }
        }
        if ($select) {
            $this->SetValue('ZoneDevices', $SlaveDeviceInstance);
        }
    }

    /**
     * Selects the audio source.
     *
     * @param int $Number
     */
    protected function SelectAudioSource(int $Number)
    {
        $select = false;
        // Device presets 1 - 6
        if ($Number >= 1 && $Number <= 6) {
            $preset = $this->SelectDevicePreset($Number);
            if ($preset) {
                $select = true;
            }
        }
        // Custom presets
        if ($Number >= 7 && $Number <= 100) {
            $customPresets = json_decode($this->ReadPropertyString('CustomPresets'));
            foreach ($customPresets as $customPreset) {
                // Add offset
                $position = $customPreset->Position + 6;
                if ($position == $Number) {
                    $volume = $customPreset->Volume;
                    $volume = $this->SetDeviceVolume($volume);
                    if (empty($volume)) {
                        $this->SetValue('VolumeSlider', $volume);
                    }
                    $source = (string) $customPreset->Source;
                    $location = (string) $customPreset->SourceSpecifications;
                    $sourceAccount = (string) $customPreset->SourceAccount;
                    switch ($source) {
                        case 'AMAZON':
                            $type = 'tracklist';
                            break;
                        case 'SPOTIFY':
                            $type = 'uri';
                            break;
                        case 'TUNEIN':
                            $type = 'stationurl';
                            break;
                        default:
                            $type = '';
                    }
                    $location = $this->SelectDeviceLocation($source, $type, $location, $sourceAccount);
                    if ($location) {
                        $select = true;
                    }
                }
            }
        }
        // Device sources
        if ($Number >= 101 && $Number <= 200) {
            /*
             * source name      source      sourceAccount
             * AUX              AUX         AUX
             * BLUETOOTH        BLUETOOTH   ""
             * HDMI_1           PRODUCT     HDMI_1
             * HDMI_2           PRODUCT     HDMI_2
             * TV               PRODUCT     TV
             *
             */
            // Get source account
            $presets = 'BST.' . $this->InstanceID . '.AudioSources';
            $associations = IPS_GetVariableProfile($presets)['Associations'];
            $sourceName = '';
            foreach ($associations as $association) {
                if ($association['Value'] == $Number) {
                    $sourceName = $association['Name'];
                }
            }
            $sourceAccount = $sourceName;
            if ($sourceName == 'BLUETOOTH') {
                $sourceAccount = '""';
            }
            // Get source
            $source = '';
            switch ($sourceName) {
                case 'HDMI_1':
                case 'HDMI_2':
                case 'TV':
                    $source = 'PRODUCT';
                    break;
                case 'AUX':
                    $source = 'AUX';
                    break;
                case 'BLUETOOTH':
                    $source = 'BLUETOOTH';
                    break;
            }
            $source = $this->SelectDeviceSource($source, $sourceAccount);
            if ($source) {
                $select = true;
            }
        }
        // Audio notifications
        if ($Number >= 201 && $Number <= 300) {
            $position = $Number - 200;
            $this->PlayAudioNotificationFromList($position);
            $select = true;
        }
        if ($select) {
            $this->SetValue('AudioSources', $Number);
        }
    }

    /**
     * Toggles the automatic power on function on or off.
     *
     * @param bool $State
     */
    protected function ToggleAutomaticPowerOn(bool $State)
    {
        // Get timer information
        $timerHour = $this->GetValue('TimerHour');
        $timerMinute = $this->GetValue('TimerMinute');
        $audioSource = $this->GetValue('TimerSources');
        // Toggle automatic power on switch on
        $this->SetValue('AutomaticPowerOn', $State);
        // Calculate timestamp and interval for next power on
        $timestamp = $this->GetTimestamp((int) $timerHour, (int) $timerMinute, 0);
        $timerInterval = ($timestamp - time()) * 1000;
        // Check use of fade in volume
        $useFadeInVolume = $this->GetValue('FadeInVolume');
        if ($audioSource <= 200 && $useFadeInVolume) {
            // Reduce next power on time by 15 minutes
            $timerInterval -= 900000;
        }
        if ($audioSource >= 201 && $useFadeInVolume) {
            // Deactivate volume fade in
            $useFadeInVolume = false;
            $this->SetValue('FadeInVolume', false);
        }
        // Activate timer
        if ($State && $timerInterval > 0) {
            // Set next power on information
            $day = date('l', $timestamp);
            $date = date('d.m.Y, H:i:s', $timestamp);
            $this->SetValue('NextPowerOn', $this->Translate($day) . ', ' . $date);
            $audioSourceName = GetValueFormatted($this->GetIDForIdent('TimerSources'));
            $this->SetValue('NextAudioSource', $audioSourceName);
            // Get next power on volume
            $targetVolume = $this->GetValue('VolumeSlider');
            // Set buffer
            $bufferData = ['audioSource' => $audioSource, 'volume' => $targetVolume, 'fadeIn' => $useFadeInVolume, 'timestamp' => $timestamp, 'firstRun' => true];
            $this->SetBuffer('AutomaticPowerOn', json_encode($bufferData));
            // Activate timer
            $this->SetTimerInterval('AutomaticPowerOn', $timerInterval);
        }
        // Deactivate timer
        if (!$State || $timerInterval < 0) {
            $this->ResetAutomaticPowerOn();
        }
    }

    /**
     * Toggles the automatic power off function.
     *
     * @param bool $State
     */
    protected function ToggleAutomaticPowerOff(bool $State)
    {
        // Check night mode
        $nightMode = $this->GetValue('NightMode');
        if (!$nightMode) {
            // Toggle automatic power off switch on
            $this->SetValue('AutomaticPowerOff', $State);
            // Get timer information
            $timerHour = $this->GetValue('TimerHour');
            $timerMinute = $this->GetValue('TimerMinute');
            $useVolumeFadeOut = $this->GetValue('FadeOutVolume');
            // Calculate timestamp and interval for next power on
            $timestamp = $this->GetTimestamp((int) $timerHour, (int) $timerMinute, 0);
            $timerInterval = ($timestamp - time()) * 1000;
            // Check use of fade out volume
            if ($useVolumeFadeOut) {
                // Reduce next power off time by 15 minutes
                $timerInterval -= 900000;
            }
            // Activate timer
            if ($State && $timerInterval > 0) {
                // Set next power off information
                $day = date('l', $timestamp);
                $date = date('d.m.Y, H:i:s', $timestamp);
                $this->SetValue('NextPowerOff', $this->Translate($day) . ', ' . $date);
                // Set buffer
                $bufferData = ['fadeOut' => $useVolumeFadeOut, 'timestamp' => $timestamp];
                $this->SetBuffer('AutomaticPowerOff', json_encode($bufferData));
                // Activate timer
                $this->SetTimerInterval('AutomaticPowerOff', $timerInterval);
            }
            // Deactivate timer
            if (!$State || $timerInterval < 0) {
                $this->ResetAutomaticPowerOff();
            }
        } else {
            echo $this->Translate('Night mode is already activated!');
        }
    }

    /**
     * Toggles the night mode function.
     *
     * @param bool $State
     */
    protected function ToggleNightMode(bool $State)
    {
        // Check automatic power off
        $automaticPowerOff = $this->GetValue('AutomaticPowerOff');
        if (!$automaticPowerOff) {
            // Toggle night mode switch
            $this->SetValue('NightMode', $State);
            $this->SetValue('FadeOutVolume', $State);
            // Activate timer
            if ($State) {
                // Set timestamp in 30 minutes
                $timestamp = time() + 1800;
                // Set next power off information
                $day = date('l', $timestamp);
                $date = date('d.m.Y, H:i:s', $timestamp);
                $this->SetValue('NextPowerOff', $this->Translate($day) . ', ' . $date);
                // Set buffer
                $bufferData = ['fadeOut' => true, 'timestamp' => $timestamp];
                $this->SetBuffer('AutomaticPowerOff', json_encode($bufferData));
                // Activate timer in 30 minutes
                $timerInterval = 900000;
                $this->SetTimerInterval('AutomaticPowerOff', $timerInterval);
            }
            // Deactivate timer
            if (!$State) {
                $this->ResetAutomaticPowerOff();
            }
        } else {
            echo $this->Translate('Automatic power off is already activated!');
        }
    }

    /**
     * Defines the module guid.
     *
     * @return string
     */
    protected function GetModuleGUID()
    {
        $moduleGUID = '{4836EF46-FF79-4D6A-91C9-FE54F1BDF2DB}';
        return $moduleGUID;
    }

    /**
     * Gets an unix timestamp by hour, minute, second.
     *
     * @param int $Hour
     * @param int $Minute
     * @param int $Second
     *
     * @return false|int
     */
    protected function GetTimestamp(int $Hour, int $Minute, int $Second)
    {
        $definedTime = $Hour . ':' . $Minute . ':' . $Second;
        if (time() >= strtotime($definedTime)) {
            $timestamp = mktime($Hour, $Minute, $Second, (int) date('n'), (int) date('j') + 1, (int) date('Y'));
        } else {
            $timestamp = mktime($Hour, $Minute, $Second, (int) date('n'), (int) date('j'), (int) date('Y'));
        }
        return $timestamp;
    }

    /**
     * Validates the configuration.
     */
    protected function ValidateConfiguration()
    {
        $this->SetStatus(102);
        // Check device name
        $deviceName = $this->ReadPropertyString('DeviceName');
        if (!empty($deviceName)) {
            IPS_SetName($this->InstanceID, $deviceName);
        }
        // Check device ip
        $deviceIP = $this->ReadPropertyString('DeviceIP');
        if (empty($deviceIP)) {
            $this->SetStatus(201);
        } else {
            $result = $this->GetDeviceNowPlaying();
            if (!is_null($result)) {
                $deviceID = (string) $result['deviceID'];
                $this->SetValue('DeviceID', $deviceID);
            }
        }
    }

    /**
     * Check the bass capability of the device.
     */
    protected function CheckBassCapabilities()
    {
        $hiddenMode = true;
        $useBass = false;
        $bassCapabilities = $this->GetDeviceBassCapabilities();
        if ($bassCapabilities) {
            $bass = $bassCapabilities->bassAvailable;
            if ($bass) {
                $hiddenMode = false;
                $useBass = true;

            }
        }
        IPS_SetHidden($this->GetIDForIdent('BassSlider'), $hiddenMode);
        $this->WriteAttributeBoolean('BassCapabilities', $useBass);
    }

    /**
     * Sets the visualisation mode for the WebFront.
     */
    protected function SetVisualisation()
    {
        // Zone control
        $showZoneControl = $this->ReadPropertyBoolean('ShowZoneControl');
        $hiddenMode = true;
        if ($showZoneControl) {
            $hiddenMode = false;
        }
        IPS_SetHidden($this->GetIDForIdent('ZoneDevices'), $hiddenMode);
        // Audio sources
        $showAudioSources = $this->ReadPropertyBoolean('ShowAudioSources');
        $hiddenMode = true;
        if ($showAudioSources) {
            $hiddenMode = false;
        }
        IPS_SetHidden($this->GetIDForIdent('AudioSources'), $hiddenMode);
        // Media information
        $showMediaInformation = $this->ReadPropertyBoolean('ShowMediaInformation');
        $hiddenMode = true;
        if ($showMediaInformation) {
            $hiddenMode = false;
        }
        IPS_SetHidden($this->GetIDForIdent('MediaInformation'), $hiddenMode);
        // Timer
        $showTimer = $this->ReadPropertyBoolean('ShowTimer');
        $hiddenMode = true;
        if ($showTimer) {
            $hiddenMode = false;
        }
        IPS_SetHidden($this->GetIDForIdent('TimerHour'), $hiddenMode);
        IPS_SetHidden($this->GetIDForIdent('TimerMinute'), $hiddenMode);
        IPS_SetHidden($this->GetIDForIdent('TimerSources'), $hiddenMode);
        IPS_SetHidden($this->GetIDForIdent('TimerVolumeSlider'), $hiddenMode);
        // Automatic power on
        $showAutomaticPowerOn = $this->ReadPropertyBoolean('ShowAutomaticPowerOn');
        $hiddenMode = true;
        if ($showAutomaticPowerOn) {
            $hiddenMode = false;
        }
        IPS_SetHidden($this->GetIDForIdent('AutomaticPowerOn'), $hiddenMode);
        IPS_SetHidden($this->GetIDForIdent('FadeInVolume'), $hiddenMode);
        IPS_SetHidden($this->GetIDForIdent('NextPowerOn'), $hiddenMode);
        IPS_SetHidden($this->GetIDForIdent('NextAudioSource'), $hiddenMode);
        // Automatic power off
        $showAutomaticPowerOff = $this->ReadPropertyBoolean('ShowAutomaticPowerOff');
        $hiddenMode = true;
        if ($showAutomaticPowerOff) {
            $hiddenMode = false;
        }
        IPS_SetHidden($this->GetIDForIdent('AutomaticPowerOff'), $hiddenMode);
        IPS_SetHidden($this->GetIDForIdent('FadeOutVolume'), $hiddenMode);
        IPS_SetHidden($this->GetIDForIdent('NightMode'), $hiddenMode);
        IPS_SetHidden($this->GetIDForIdent('NextPowerOff'), $hiddenMode);
    }

    /**
     * Resets the automatic power on function.
     */
    protected function ResetAutomaticPowerOn()
    {
        // Toggle automatic power on switch off
        $this->SetValue('AutomaticPowerOn', false);
        // Toggle fade in volume switch off
        $this->SetValue('FadeInVolume', false);
        // Reset next power on information
        $this->SetValue('NextPowerOn', '');
        // Reset next audio source
        $this->SetValue('NextAudioSource', '');
        // Stop timer
        $this->SetTimerInterval('AutomaticPowerOn', 0);
        // Reset buffer
        $bufferData = ['audioSource' => 0, 'volume' => 0, 'fadeIn' => false, 'timestamp' => 0, 'firstRun' => true];
        $this->SetBuffer('AutomaticPowerOn', json_encode($bufferData));
    }

    /**
     * Resets the automatic power off function.
     */
    protected function ResetAutomaticPowerOff()
    {
        // Toggle automatic power off switch off
        $this->SetValue('AutomaticPowerOff', false);
        // Toggle fade out volume switch off
        $this->SetValue('FadeOutVolume', false);
        // Toggle night mode switch off
        $this->SetValue('NightMode', false);
        // Reset next power on information
        $this->SetValue('NextPowerOff', '');
        // Stop timer
        $this->SetTimerInterval('AutomaticPowerOff', 0);
        // Reset buffer
        $bufferData = ['fadeOut' => false, 'timestamp' => 0];
        $this->SetBuffer('AutomaticPowerOff', json_encode($bufferData));
    }

    /**
     * Checks if the device is alive.
     *
     * @return bool|null
     */
    protected function CheckDeviceReachability()
    {
        $deviceIP = $this->ReadPropertyString('DeviceIP');
        $timeout = $this->ReadPropertyInteger('Timeout');
        $alive = null;
        if (!empty($deviceIP) && $timeout > 0) {
            if (Sys_Ping($deviceIP, $timeout) == true) {
                $alive = true;
            }
        }
        return $alive;
    }

    //#################### Public module functions

    /**
     * Updates the information.
     */
    public function UpdateInformation()
    {
        $nowPlaying = $this->GetDeviceNowPlaying();
        if (!is_null($nowPlaying)) {
            // Device power
            $deviceMode = (string) $nowPlaying->attributes()->source;
            $devicePower = false;
            if ($deviceMode != 'STANDBY') {
                $devicePower = true;
            }
            SetValue($this->GetIDForIdent('DevicePower'), $devicePower);
            // Play pause
            $playMode = $nowPlaying->playStatus;
            switch ($playMode) {
                case 'PAUSE_STATE':
                case 'STOP_STATE':
                case 'INVALID_PLAY_STATUS':
                    $value = 1;
                    break;
                default:
                    $value = 2;
                    break;
            }
            $this->SetValue('PlayMode', $value);
            // Presets
            $source = (string) $nowPlaying->attributes()->source;
            $location = null;
            if (@isset($nowPlaying->ContentItem->attributes()->location)) {
                $location = (string) $nowPlaying->ContentItem->attributes()->location;
            }
            $audioLocations = 'BST.' . $this->InstanceID . '.AudioLocations';
            $associations = IPS_GetVariableProfile($audioLocations)['Associations'];
            $sourceNumber = 0;
            foreach ($associations as $association) {
                if ($association['Name'] == $location) {
                    $sourceNumber = $association['Value'];
                }
            }
            if ($sourceNumber == 0) {
                foreach ($associations as $association) {
                    if ($association['Name'] == $source) {
                        $sourceNumber = $association['Value'];
                    }
                }
            }
            if ($sourceNumber != 0) {
                $actualSource = $this->GetValue('AudioSources');
                if ($actualSource != $sourceNumber) {
                    $this->SetValue('AudioSources', $sourceNumber);
                }
            }
            // Cover
            if ($deviceMode == 'STANDBY' || @!isset($nowPlaying->art->attributes()->artImageStatus)) {
                $cover = 'https://raw.githubusercontent.com/ubittner/SymconBoseSoundTouch/master/imgs/bose_logo_white.png';
            } else {
                $cover = (string) ($nowPlaying->art);
                $imageStatus = (string) $nowPlaying->art->attributes()->artImageStatus;
                if ($imageStatus == 'SHOW_DEFAULT_IMAGE') {
                    $cover = 'https://raw.githubusercontent.com/ubittner/SymconBoseSoundTouch/master/imgs/bose_logo_white.png';
                }
            }
            // Name
            $name = (string) $nowPlaying->ContentItem->itemName;
            // Artist
            $artist = utf8_decode((string) $nowPlaying->artist);
            // Track
            $track = utf8_decode((string) $nowPlaying->track);
            // Album
            $album = utf8_decode((string) $nowPlaying->album);
            // Media Information
            $mediaInformation = '<!doctype html>
            <html lang="de">
            <head>
            <meta charset="utf-8">
            <title>Media Info</title>
            <style type="text/css">
            .cover {
                display: block;
                float: left;
                padding-left: 8px;
                padding-top: 8px;
                padding-right: 8px;
                padding-bottom: 8px;
            }
            .mediainfo .cover {
                -webkit-box-shadow: 2px 2px 5px hsla(0,0%,0%,1.00);
               box-shadow: 2px 2px 5px hsla(0,0%,0%,1.00);
            }
            .description {
                vertical-align: bottom;
                float: none;
                padding-top: 33px;
                padding-right: 21px;
                padding-bottom: 21px;
                margin-top: 0;
                margin-left: 170px;
            }
            .source {
                font-size: initial;
            }
            .artist {
                font-size: initial;
            }
            .track {
                font-size: initial;
            }
            .album {
                font-size: initial;
            }
            </style>
            </head>
            <body>
            <main class="mediainfo">
              <section class="cover"><img src="' . $cover . '" width="145" height="145" id="cover" alt="cover"></section>
              <section class="description">
                <div class="source">' . $source . '</div>
                <div class="source">' . $name . '</div>
                <div class="artist">' . $artist . '</div>
                <div class="track">' . $track . '</div>
                <div class="album">' . $album . '</div>
              </section>
            </main>
            </body>
            </html>';
            $this->SetValue('MediaInformation', $mediaInformation);
        }
        // Volume
        $volume = $this->GetDeviceVolume();
        if (!is_null($volume)) {
            $actualVolume = (int) $volume->targetvolume;
            if ($volume) {
                $this->SetValue('VolumeSlider', $actualVolume);
            }
        }
        // Bass
        if ($this->ReadAttributeBoolean('BassCapabilities')) {
            $bass = $this->GetDeviceBass();
            if (!is_null($bass)) {
                $actualBass = (int) $bass->actualbass;
                if ($bass) {
                    $this->SetValue('BassSlider', $actualBass);
                }
            }
        }
        // Zone device
        $getZone = $this->GetDeviceZone();
        if (!is_null($getZone)) {
            $member = (string) $getZone->member;
            if (empty($member)) {
                $this->SetValue('ZoneDevices', 0);
            }
        }
    }

    /**
     * Fades out the volume to 0.
     */
    public function FadeOutPlayback()
    {
        // Get device volume
        $deviceVolume = $this->GetDeviceVolume();
        $actualVolume = (int) $deviceVolume->actualvolume;
        // Get device status
        $deviceMode = $this->GetDeviceNowPlaying();
        $source = (string) $deviceMode->attributes()->source;
        // Check if device is already powered off
        if ($source == 'STANDBY') {
            $this->SetTimerInterval('FadeOutPlayback', 0);
        } else {
            if ($actualVolume > 1) {
                // Decrease volume by 1
                $this->SetDeviceVolume($actualVolume - 1);
                $this->SetValue('VolumeSlider', $actualVolume - 1);
                // Set next timer interval
                $this->SetTimerInterval('FadeOutPlayback', 1000);
            } else {
                $this->PowerDevice();
                $this->SetValue('DevicePower', false);
                $this->SetTimerInterval('FadeOutPlayback', 0);
            }
        }
    }

    /**
     * Executes the automatic power on function.
     */
    public function ExecuteAutomaticPowerOn()
    {
        // Get buffer data
        $bufferData = json_decode($this->GetBuffer('AutomaticPowerOn'));
        $audioSource = (int) $bufferData->audioSource;
        $volume = (int) $bufferData->volume;
        $fadeIn = (bool) $bufferData->fadeIn;
        $timestamp = (int) $bufferData->timestamp;
        $firstRun = (bool) $bufferData->firstRun;
        // Get device volume
        $deviceVolume = $this->GetDeviceVolume();
        $actualVolume = (int) $deviceVolume->actualvolume;
        // Normal mode
        if ($audioSource != 0 && $fadeIn == false && $firstRun) {
            // Get device status
            $deviceMode = $this->GetDeviceNowPlaying();
            $source = (string) $deviceMode->attributes()->source;
            // Check if device is already powered on
            if ($source != 'STANDBY') {
                // Reset automatic power on
                $this->ResetAutomaticPowerOn();
            } else {
                // Toggle device power switch on
                $this->SetValue('DevicePower', true);
                // Select audio source
                $this->SelectAudioSource($audioSource);
                $this->SetValue('AudioSources', $audioSource);
                // Set volume
                $this->SetDeviceVolume($volume);
                $this->SetValue('VolumeSlider', $volume);
                // Reset
                $this->ResetAutomaticPowerOn();
            }
        }
        // Fade in volume mode
        if ($audioSource != 0 && $fadeIn) {
            // First run
            if ($firstRun) {
                // Get device status
                $deviceMode = $this->GetDeviceNowPlaying();
                $source = (string) $deviceMode->attributes()->source;
                // Check if device is already powered on
                if ($source != 'STANDBY') {
                    // Reset automatic power on
                    $this->ResetAutomaticPowerOn();
                } else {
                    // Toggle device power switch on
                    $this->SetValue('DevicePower', true);
                    // Select preset
                    $this->SelectAudioSource($audioSource);
                    $this->SetValue('AudioSources', $audioSource);
                    // Set volume
                    $this->SetDeviceVolume(1);
                    $this->SetValue('VolumeSlider', 1);
                    // Change next power on information
                    $day = date('l', $timestamp);
                    $date = date('d.m.Y, H:i:s', $timestamp);
                    $this->SetValue('NextPowerOn', $this->Translate('Timer running until') . ' ' . $this->Translate($day) . ', ' . $date);
                    // Set buffer
                    $bufferData = ['audioSource' => $audioSource, 'volume' => $volume, 'fadeIn' => true, 'timestamp' => $timestamp, 'firstRun' => false];
                    $this->SetBuffer('AutomaticPowerOn', json_encode($bufferData));
                    // Set next timer interval
                    $timerInterval = ($timestamp - time()) / ($volume - 1);
                    $this->SetTimerInterval('AutomaticPowerOn', $timerInterval * 1000);
                }
            }
            // Next run
            if (!$firstRun) {
                if ($actualVolume <= $volume) {
                    // Increase volume by 1
                    $this->SetDeviceVolume($actualVolume + 1);
                    $this->SetValue('VolumeSlider', $actualVolume + 1);
                    // Set buffer
                    $bufferData = ['audioSource' => $audioSource, 'volume' => $volume, 'fadeIn' => true, 'timestamp' => $timestamp, 'firstRun' => false];
                    $this->SetBuffer('AutomaticPowerOn', json_encode($bufferData));
                    // Set next timer interval
                    if ($timestamp > time()) {
                        $timerInterval = ($timestamp - time()) / ($volume - ($actualVolume + 1));
                        $this->SetTimerInterval('AutomaticPowerOn', $timerInterval * 1000);
                    } else {
                        // Reset automatic power on
                        $this->ResetAutomaticPowerOn();
                    }
                } else {
                    // Reset automatic power on
                    $this->ResetAutomaticPowerOn();
                }
            }
        }
    }

    /**
     * Executes the automatic power off function.
     */
    public function ExecuteAutomaticPowerOff()
    {
        // Get buffer data
        $bufferData = json_decode($this->GetBuffer('AutomaticPowerOff'));
        $fadeOut = (bool) $bufferData->fadeOut;
        $timestamp = (int) $bufferData->timestamp;
        // Get device volume
        $deviceVolume = $this->GetDeviceVolume();
        $actualVolume = (int) $deviceVolume->actualvolume;
        // Normal mode
        if (!$fadeOut) {
            // Get device status
            $deviceMode = $this->GetDeviceNowPlaying();
            $source = (string) $deviceMode->attributes()->source;
            // Check if device is already powered off
            if ($source == 'STANDBY') {
                $this->ResetAutomaticPowerOff();
            } else {
                // Power device off
                $this->PowerDevice();
                $this->SetValue('DevicePower', false);
                // Reset automatic power off
                $this->ResetAutomaticPowerOff();
            }
        }
        // Fade out volume mode
        if ($fadeOut) {
            // Get device status
            $deviceMode = $this->GetDeviceNowPlaying();
            $source = (string) $deviceMode->attributes()->source;
            // Check if device is already powered off
            if ($source == 'STANDBY') {
                $this->ResetAutomaticPowerOff();
            } else {
                if ($actualVolume > 1) {
                    // Decrease volume by 1
                    $this->SetDeviceVolume($actualVolume - 1);
                    $this->SetValue('VolumeSlider', $actualVolume - 1);
                    // Change next power off information
                    $day = date('l', $timestamp);
                    $date = date('d.m.Y, H:i:s', $timestamp);
                    $this->SetValue('NextPowerOff', $this->Translate('Timer running until') . ' ' . $this->Translate($day) . ', ' . $date);
                    // Set next timer interval
                    if ($timestamp > time()) {
                        $timerInterval = ($timestamp - time()) / ($actualVolume - 1);
                        $this->SetTimerInterval('AutomaticPowerOff', $timerInterval * 1000);
                    } else {
                        $this->PowerDevice();
                        $this->SetValue('DevicePower', false);
                        $this->ResetAutomaticPowerOff();
                    }
                } else {
                    $this->PowerDevice();
                    $this->SetValue('DevicePower', false);
                    $this->ResetAutomaticPowerOff();
                }
            }
        }
    }

    /**
     * Publishs the media file to webfront/user/media/.
     */
    public function PublishMediaFile()
    {
        $kernelPlatform = IPS_GetKernelPlatform();
        $kernelDir = IPS_GetKernelDir();
        $mediaID = $this->ReadPropertyInteger('MediaFile');
        if ($mediaID != 0) {
            $mediaFile = IPS_GetMedia($mediaID)['MediaFile'];
            switch ($kernelPlatform) {
                case 'Windows x86':
                case 'Windows x64':
                    /*
                    $mediaFile = str_replace('/', '\\', $mediaFile);
                    ob_start();
                    shell_exec('mkdir ' . $kernelDir . 'webfront\\user\\media');
                    ob_end_clean();
                    ob_start();
                    shell_exec('copy ' . $kernelDir . $mediaFile . ' ' . $kernelDir . 'webfront\\user\\' . $mediaFile);
                    ob_end_clean();
                    echo $this->Translate('The media file was published successfully.');
                    break;
                    */
                    $mediaFile = str_replace('/', '\\', $mediaFile);
                    ob_start();
                    shell_exec('mkdir ' . $kernelDir . 'webfront\\user\\media');
                    ob_end_clean();
                    ob_start();
                    shell_exec('mklink /H ' . $kernelDir . $mediaFile . ' ' . $kernelDir . 'webfront\\user\\' . $mediaFile);
                    ob_end_clean();
                    echo $this->Translate('The media file was published successfully.');
                    break;
                case 'Mac':
                    /*
                    $kernelDir = str_replace(' ', '\ ', $kernelDir);
                    shell_exec('mkdir ' . $kernelDir . 'webfront/user/media/');
                    shell_exec('cp ' . $kernelDir . $mediaFile . ' ' . $kernelDir . 'webfront/user/' . $mediaFile);
                    echo $this->Translate('The media file was published successfully.');
                    break;
                    */
                    $kernelDir = str_replace(' ', '\ ', $kernelDir);
                    shell_exec('mkdir ' . $kernelDir . 'webfront/user/media/');
                    shell_exec('ln ' . $kernelDir . $mediaFile . ' ' . $kernelDir . 'webfront/user/' . $mediaFile);
                    echo $this->Translate('The media file was published successfully.');
                    break;
                case 'Ubuntu':
                case 'RaspberryPi':
                case 'Raspberry Pi':
                case 'SymBox':
                case 'Alpine':
                    /*
                    shell_exec('mkdir ' . $kernelDir . 'webfront/user/media/');
                    shell_exec('cp ' . $kernelDir . $mediaFile . ' ' . $kernelDir . 'webfront/user/' . $mediaFile);
                    echo $this->Translate('The media file was published successfully.');
                    break;
                    */
                    shell_exec('mkdir ' . $kernelDir . 'webfront/user/media/');
                    shell_exec('ln ' . $kernelDir . $mediaFile . ' ' . $kernelDir . 'webfront/user/' . $mediaFile);
                    echo $this->Translate('The media file was published successfully.');
                    break;
            }
        } else {
            echo $this->Translate('Please select a media file under point (3) Media files!');
        }
    }

    /**
     * Gets endpoint data from the device.
     *
     * @param string $Endpoint
     *
     * @return null|SimpleXMLElement
     */
    public function GetDataFromDevice(string $Endpoint)
    {
        $xmlData = null;
        $checkDevice = $this->CheckDeviceReachability();
        if ($checkDevice) {
            $xmlData = null;
            $deviceIP = $this->ReadPropertyString('DeviceIP');
            $url = 'http://' . $deviceIP . ':8090/' . $Endpoint;
            $curl = curl_init();
            curl_setopt_array($curl, [CURLOPT_URL => $url,
                CURLOPT_HEADER                    => 0,
                CURLOPT_RETURNTRANSFER            => 1]);
            $result = curl_exec($curl);
            curl_close($curl);
            /*
            <?xml version="1.0" ?>
            <errors deviceID="$STRING">
              <error value="$INT" name="$STRING" severity="$STRING">$STRING</error>
              ...
            </errors>
             */
            if (!preg_match('/<title>Object Not Found<\/title>/', $result)) {
                $xmlData = new SimpleXMLElement($result);
            }
        }
        return $xmlData;
    }

    /**
     * Sends postfield data to the endpoint of the device.
     *
     * @param string $Endpoint
     * @param string $Postfields
     *
     * @return null|SimpleXMLElement
     */
    public function SendDataToDevice(string $Endpoint, string $Postfields)
    {
        $xmlData = null;
        $checkDevice = $this->CheckDeviceReachability();
        if ($checkDevice) {
            $deviceIP = $this->ReadPropertyString('DeviceIP');
            $url = 'http://' . $deviceIP . ':8090/' . $Endpoint;
            $curl = curl_init();
            curl_setopt_array($curl, [CURLOPT_URL => $url,
                CURLOPT_HEADER                    => 0,
                CURLOPT_RETURNTRANSFER            => 1,
                CURLOPT_POST                      => 1,
                CURLOPT_POSTFIELDS                => $Postfields,
                CURLOPT_HTTPHEADER                => ['Content-type: text/xml']]);
            $result = curl_exec($curl);
            curl_close($curl);
            $xmlData = new SimpleXMLElement($result);
        }
        return $xmlData;
    }

    //#################### Profiles

    /**
     * Creates the profiles for member devices.
     */
    protected function CreateZoneDevicesProfile()
    {
        $moduleGUID = $this->GetModuleGUID();
        $instances = IPS_GetInstanceListByModuleID($moduleGUID);
        foreach ($instances as $instance) {
            $memberDevices = 'BST.' . $instance . '.ZoneDevices';
            // Delete zone devices first
            if (IPS_VariableProfileExists($memberDevices)) {
                IPS_DeleteVariableProfile($memberDevices);
            }
            // Create profile
            IPS_CreateVariableProfile($memberDevices, 1);
            IPS_SetVariableProfileIcon($memberDevices, 'Network');
            // Add devices
            foreach ($instances as $device) {
                if ($instance == $device) {
                    IPS_SetVariableProfileAssociation($memberDevices, 0, $this->Translate('Off'), '', 0xFF0000);
                } else {
                    $instanceName = IPS_GetName($device);
                    if (!empty($instanceName)) {
                        IPS_SetVariableProfileAssociation($memberDevices, $device, '' . $instanceName . '', '', 0x0000FF);
                    }
                }
            }
        }
    }

    /**
     * Creates the audio sources, audio locations and timer sources profiles.
     */
    protected function CreateAudioSourcesProfile()
    {
        $audioSources = 'BST.' . $this->InstanceID . '.AudioSources';
        $audioLocations = 'BST.' . $this->InstanceID . '.AudioLocations';
        $timerSources = 'BST.' . $this->InstanceID . '.TimerSources';
        // Delete existing profiles first
        if (IPS_VariableProfileExists($audioSources)) {
            IPS_DeleteVariableProfile($audioSources);
        }
        if (IPS_VariableProfileExists($audioLocations)) {
            IPS_DeleteVariableProfile($audioLocations);
        }
        if (IPS_VariableProfileExists($timerSources)) {
            IPS_DeleteVariableProfile($timerSources);
        }
        // Create profiles again
        IPS_CreateVariableProfile($audioSources, 1);
        IPS_SetVariableProfileIcon($audioSources, 'Database');

        IPS_CreateVariableProfile($audioLocations, 1);
        IPS_SetVariableProfileIcon($audioLocations, 'Database');

        IPS_CreateVariableProfile($timerSources, 1);
        IPS_SetVariableProfileIcon($timerSources, 'Execute');
        // Add device presets 1 - 6
        $presets = $this->GetDevicePresets();
        if ($presets) {
            $i = 1;
            foreach ($presets as $preset) {
                $presetNumber = (int) $preset->attributes()->id;
                $presetID = (string) $preset->ContentItem->attributes()->location;
                $presetName = (string) $preset->ContentItem->itemName;
                IPS_SetVariableProfileAssociation($audioSources, $presetNumber, $presetName, '', 0x0000FF);
                IPS_SetVariableProfileAssociation($audioLocations, $presetNumber, $presetID, '', 0x0000FF);
                IPS_SetVariableProfileAssociation($timerSources, $presetNumber, $presetName, '', 0x0000FF);
                $i++;
            }
        }
        // Add custom presets
        $offset = 6;
        $customPresets = json_decode($this->ReadPropertyString('CustomPresets'));
        if ($customPresets) {
            foreach ($customPresets as $customPreset) {
                IPS_SetVariableProfileAssociation($audioSources, $customPreset->Position + $offset, $customPreset->Description, '', 0x0000FF);
                IPS_SetVariableProfileAssociation($audioLocations, $customPreset->Position + $offset, $customPreset->SourceSpecifications, '', 0x0000FF);
                IPS_SetVariableProfileAssociation($timerSources, $customPreset->Position + $offset, $customPreset->Description, '', 0x0000FF);
            }
        }
        // Add device sources
        $definedSources = ['AUX', 'BLUETOOTH', 'HDMI_1', 'HDMI_2', 'TV'];
        $deviceSources = $this->GetDeviceSources();
        if ($deviceSources) {
            $sourceItems = $deviceSources->sourceItem;
            $availableSources = [];
            foreach ($sourceItems as $sourceItem) {
                $attribute = $sourceItem->attributes();
                if (isset($attribute)) {
                    $sourceItemAttribute = $attribute->source;
                    foreach ($definedSources as $definedSource) {
                        if ($sourceItemAttribute == $definedSource) {
                            $availableSources[] = $definedSource;
                        }
                    }
                }
                foreach ($definedSources as $definedSource) {
                    if ($sourceItem == $definedSource) {
                        $availableSources[] = $definedSource;
                    }
                }
            }
            $k = 101;
            if (!empty($availableSources)) {
                foreach ($availableSources as $availableSource) {
                    IPS_SetVariableProfileAssociation($audioSources, $k, $availableSource, '', 0x0000FF);
                    IPS_SetVariableProfileAssociation($audioLocations, $k, $availableSource, '', 0x0000FF);
                    IPS_SetVariableProfileAssociation($timerSources, $k, $availableSource, '', 0x0000FF);
                    $k++;
                }
            }
            // Add audio notifications
            $l = 201;
            $audioNotifications = json_decode($this->ReadPropertyString('AudioNotifications'), true);
            if (!empty($audioNotifications)) {
                foreach ($audioNotifications as $audioNotification) {
                    $index = $audioNotification['Position'];
                    $name = $audioNotification['Description'];
                    // Create association
                    if (!empty($index) && !empty($name)) {
                        IPS_SetVariableProfileAssociation($audioSources, $l, $name, '', 0x0000FF);
                        IPS_SetVariableProfileAssociation($audioLocations, $l, $name, '', 0x0000FF);
                        IPS_SetVariableProfileAssociation($timerSources, $l, $name, '', 0x0000FF);
                    }
                    $l++;
                }
            }
        }
    }

    /**
     * Deletes the created profiles for this instance.
     */
    protected function DeleteProfiles()
    {
        // Volume slider
        $volumeSlider = 'BST.' . $this->InstanceID . '.VolumeSlider';
        if (IPS_VariableProfileExists($volumeSlider)) {
            IPS_DeleteVariableProfile($volumeSlider);
        }
        // Bass slider
        $bassSlider = 'BST.' . $this->InstanceID . '.BassSlider';
        if (IPS_VariableProfileExists($bassSlider)) {
            IPS_DeleteVariableProfile($bassSlider);
        }
        // Play mode
        $playMode = 'BST.' . $this->InstanceID . '.PlayMode';
        if (IPS_VariableProfileExists($playMode)) {
            IPS_DeleteVariableProfile($playMode);
        }
        // Zone devices
        $zoneDevices = 'BST.' . $this->InstanceID . '.ZoneDevices';
        if (IPS_VariableProfileExists($zoneDevices)) {
            IPS_DeleteVariableProfile($zoneDevices);
        }
        // Audio sources
        $audioSources = 'BST.' . $this->InstanceID . '.AudioSources';
        if (IPS_VariableProfileExists($audioSources)) {
            IPS_DeleteVariableProfile($audioSources);
        }
        // Audio locations
        $audioLocations = 'BST.' . $this->InstanceID . '.AudioLocations';
        if (IPS_VariableProfileExists($audioLocations)) {
            IPS_DeleteVariableProfile($audioLocations);
        }
        // Timer hour
        $timerHour = 'BST.' . $this->InstanceID . '.TimerHour';
        if (IPS_VariableProfileExists($timerHour)) {
            IPS_DeleteVariableProfile($timerHour);
        }
        // Timer minute
        $timerMinute = 'BST.' . $this->InstanceID . '.TimerMinute';
        if (IPS_VariableProfileExists($timerMinute)) {
            IPS_DeleteVariableProfile($timerMinute);
        }
        // Timer sources
        $timerMinute = 'BST.' . $this->InstanceID . '.TimerSources';
        if (IPS_VariableProfileExists($timerMinute)) {
            IPS_DeleteVariableProfile($timerMinute);
        }
    }
}
