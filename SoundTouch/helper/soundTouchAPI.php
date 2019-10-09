<?php

declare(strict_types=1);
trait soundTouchAPI
{
    //#################### GET methods

    /**
     * Gets basic information about a product.
     * This endpoint returns an object containing an assortment of product information.
     *
     * @return mixed
     */
    public function GetDeviceInfo()
    {
        $endpoint = 'info';
        $result = $this->GetDataFromDevice($endpoint);
        return $result;
        /*
        examples:
        (string)$info->attributes()->deviceID
        (string)$info->name
        (string)$info->type
        (string)$info->margeAccountUUID
        (string)$info->components->component[0]->componentCategory
        (string)$info->components->component[0]->softwareVersion
        (string)$info->components->component[0]->serialNumber
        (string)$info->margeURL
        (string)$info->networkInfo[0]->attributes()->type
        (string)$info->networkInfo[0]->macAddress
        (string)$info->networkInfo[0]->ipAddress
        (string)$info->moduleType
        (string)$info->variant
        (string)$info->variantMode
        (string)$info->countryCode
        (string)$info->regionCode
         */
    }

    /**
     * Gets information about what's playing on a product.
     * This endpoint returns an object containing all information about currently playing media.
     * The information provided varies by source.
     *
     * @return mixed
     */
    public function GetDeviceNowPlaying()
    {
        $endpoint = 'now_playing';
        $result = $this->GetDataFromDevice($endpoint);
        return $result;
        /*
        examples:
        (string)$nowPlaying->attributes()->deviceID
        (string)$nowPlaying->attributes()->source
        (string)$nowPlaying->ContentItem->attributes()->source
        (string)$nowPlaying->ContentItem->attributes()->location
        (string)$nowPlaying->ContentItem->attributes()->sourceAccount
        (bool)$nowPlaying->ContentItem->attributes()->isPresetable
        (string)$nowPlaying->ContentItem->itemName
        (string)$nowPlaying->ContentItem->containerArt
        (string)utf8_decode($nowPlaying->track)
        (string)utf8_decode($nowPlaying->artist)
        (string)utf8_decode($nowPlaying->album)
        (string)utf8_decode($nowPlaying->stationName)
        (string)($nowPlaying->art)
        (string)($nowPlaying->playStatus)
        (string)utf8_decode($nowPlaying->description)
        (string)utf8_decode($nowPlaying->stationLocation))
         */
    }

    /**
     * Gets the current volume and mute status of a product.
     * This endpoint returns an object containing all information regarding volume of a product.
     * The values of targetvolume and actualvolume will only be different when the volume is changing.
     *
     * @return mixed
     */
    public function GetDeviceVolume()
    {
        $endpoint = 'volume';
        $result = $this->GetDataFromDevice($endpoint);
        return $result;
        /*
        examples:
        (string)$volume->attributes()->deviceID
        (int)$volume->targetvolume
        (int)$volume->actualvolume
        (bool)$volume->muteenabled
         */
    }

    /**
     * Gets available sources for a product.
     * Lists all available sources previously registered to product.
     * This includes both streaming sources (Spotify, Pandora, etc.) and local sources (AUX, Bluetooth, etc.).
     * This endpoint is intended to be used for local sources (in conjunction with /select).
     *
     * @return mixed
     */
    public function GetDeviceSources()
    {
        $endpoint = 'sources';
        $result = $this->GetDataFromDevice($endpoint);
        return $result;
        /*
        examples:
        (string)$deviceSources->attributes()->deviceID
         */
    }

    /**
     * Gets the current multiroom zone state of a product.
     * This endpoint returns an object containing the zone of the current product and the other members of the zone.
     * If the product is not in a zone, the response simply contains </zone>.
     * Otherwise the endpoint will return a zone object.
     *
     * @return mixed
     */
    public function GetDeviceZone()
    {
        $endpoint = 'getZone';
        $result = $this->GetDataFromDevice($endpoint);
        return $result;
        /*
        examples:
        (string)$deviceZoneData->attributes()->master
        (string)$deviceZoneData->member
         */
    }

    /**
     * Gets whether a product supports reducing bass.
     * Some products can be commanded to reduce their bass level.
     * This endpoint returns an object containing information on whether the target product supports this capability,
     * and information for its use.
     *
     * @return mixed
     */
    public function GetDeviceBassCapabilities()
    {
        $endpoint = 'bassCapabilities';
        $result = $this->GetDataFromDevice($endpoint);
        return $result;
        /*
        examples:
        (string)$bassCapabilities->attributes()->deviceID
        (bool)$bassCapabilities->bassAvailable
        (int)$bassCapabilities->bassMin
        (int)$bassCapabilities->bassMax
        (int)$bassCapabilities->bassDefault
         */
    }

    /**
     * Gets the bass level of a product, if supported.
     * If supported, this endpoint returns an object containing the product's bass level.
     * Use the /bassCapabilities endpoint to find whether a given product supports bass control and the ranges.
     * The values of 'targetbass' and 'actualbass' will only be different when bass level is changing.
     *
     * @return mixed
     */
    public function GetDeviceBass()
    {
        $endpoint = 'bass';
        $result = $this->GetDataFromDevice($endpoint);
        return $result;
        /*
        examples:
        (string)$bass->attributes()->deviceID
        (int)$bass->targetbass
        (int)$bass->actualbass
         */
    }

    /**
     * Gets information related to the user's SoundTouch presets.
     * This endpoint returns an object containing information for each of the user's presets.
     *
     * @return mixed
     */
    public function GetDevicePresets()
    {
        $endpoint = 'presets';
        $result = $this->GetDataFromDevice($endpoint);
        return $result;
        /*
        examples:
        (int)$devicePresets->preset[$i]->attributes()->id
        (int)$devicePresets->preset[$i]->attributes()->createdOn
        (int)$devicePresets->preset[$i]->attributes()->updatedOn
        (string)$devicePresets->preset[$i]->ContentItem->attributes()->source
        (string)$devicePresets->preset[$i]->ContentItem->attributes()->location
        (string)$devicePresets->preset[$i]->ContentItem->attributes()->sourceAccount
        (bool)$devicePresets->preset[$i]->ContentItem->attributes()->isPresetable
        (string)utf8_decode($devicePresets->preset[$i]->ContentItem->itemName)
        (string)$devicePresets->preset[$i]->ContentItem->containerArt
         */
    }

    /**
     * Gets the current left/right stereo pair configuration of a product.
     * This endpoint returns an object containing the Stereo Pair Group configuration of the current product and the other member of the group.
     * Currently, only the SoundTouch 10 supports stereo pair groups.
     * If the product is not in a group, the response simply contains </group>. Otherwise the endpoint will return a group object.
     *
     * @return mixed
     */
    public function GetDeviceGroup()
    {
        $endpoint = 'getGroup';
        $result = $this->GetDataFromDevice($endpoint);
        return $result;
    }

    /**
     * Checks if the device can receive and play audio notifications.
     * It must be either SoundTouch 10, SoundTouch 20, or SoundTouch 30, and have a moduleType equal to sm2, and its firmware version.
     */
    protected function CheckAudioNotificationFunctionality()
    {
        $showAudioNotifications = $this->ReadPropertyBoolean('ShowAudioNotifications');
        if ($showAudioNotifications) {
            $endpoint = 'info';
            $result = $this->GetDataFromDevice($endpoint);
            $hiddenMode = false;
            if ($result) {
                $moduleType = $result->moduleType;
                if ($moduleType != 'sm2') {
                    $hiddenMode = true;
                }
            }
            IPS_SetHidden($this->GetIDForIdent('AudioNotifications'), $hiddenMode);
        }
    }

    //#################### POST methods

    /**
     * Toggles device on/off.
     * Keys are used as a primary means to interact with the SoundTouch product for many common control functions.
     * They act exactly like pressing a key on a remote control, or the SoundTouch product.
     * To simulate pressing a key, you must build a key parameter object.
     * For this our purposes, on/off refers to waking a product from, or putting a product into its standby state.
     * Products in standby maintain network connections.
     * You can determine if a product is in standby using the /now_playing endpoint.
     *
     * @return mixed
     */
    public function PowerDevice()
    {
        $endpoint = 'key';
        $postfields = '<key state=press sender=Gabbo>POWER</key>';
        $this->SendDataToDevice($endpoint, $postfields);
        $postfields = '<key state=release sender=Gabbo>POWER</key>';
        $result = $this->SendDataToDevice($endpoint, $postfields);
        return $result;
    }

    /**
     * Plays or sets Preset 1-6 (depending on key state).
     * Keys are used as a primary means to interact with the SoundTouch product for many common control functions.
     * They act exactly like pressing a key on a remote control, or the SoundTouch product.
     * To simulate pressing a key, you must build a key parameter object.
     * For the following value entries, set state to be "release" to start playing the preset,
     * or set the state to "press" to set the preset.
     * You can get a list of the current presets using /presets.
     *
     * @param int $Preset
     *
     * @return mixed
     */
    public function SelectDevicePreset(int $Preset)
    {
        $endpoint = 'key';
        $postfields = '';
        if ($Preset >= 1 || $Preset <= 6) {
            $postfields = '<key state=release sender=Gabbo>PRESET_' . $Preset . '</key>';
        }
        $result = $this->SendDataToDevice($endpoint, $postfields);
        return $result;
    }

    /**
     * Toggles play/pause for the stream.
     * Keys are used as a primary means to interact with the SoundTouch product for many common control functions.
     * They act exactly like pressing a key on a remote control, or the SoundTouch product.
     * To simulate pressing a key, you must build a key parameter object.
     *
     * @return mixed
     */
    public function ToggleDevicePlayPause()
    {
        $endpoint = 'key';
        $postfields = '<key state=press sender=Gabbo>PLAY_PAUSE</key>';
        $result = $this->SendDataToDevice($endpoint, $postfields);
        return $result;
    }

    /**
     * Toggles mute for product.
     * Keys are used as a primary means to interact with the SoundTouch product for many common control functions.
     * They act exactly like pressing a key on a remote control, or the SoundTouch product.
     * To simulate pressing a key, you must build a key parameter object.
     *
     * @return mixed
     */
    public function ToggleDeviceMute()
    {
        $endpoint = 'key';
        $postfields = '<key state=press sender=Gabbo>MUTE</key>';
        $result = $this->SendDataToDevice($endpoint, $postfields);
        return $result;
    }

    /**
     * Skips to the previous track.
     * See /now_playing for guidance on when the key is valid.
     * Keys are used as a primary means to interact with the SoundTouch product for many common control functions.
     * They act exactly like pressing a key on a remote control, or the SoundTouch product.
     * To simulate pressing a key, you must build a key parameter object.
     *
     * @return mixed
     */
    public function SelectDevicePreviousTrack()
    {
        $endpoint = 'key';
        $postfields = '<key state=press sender=Gabbo>PREV_TRACK</key>';
        $result = $this->SendDataToDevice($endpoint, $postfields);
        return $result;
    }

    /**
     * Skips to the next track. This may not be available for all content types.
     * See /now_playing for guidance on when the key is valid.
     * Keys are used as a primary means to interact with the SoundTouch product for many common control functions.
     * They act exactly like pressing a key on a remote control, or the SoundTouch product.
     * To simulate pressing a key, you must build a key parameter object.
     *
     * @return mixed
     */
    public function SelectDeviceNextTrack()
    {
        $endpoint = 'key';
        $postfields = '<key state=press sender=Gabbo>NEXT_TRACK</key>';
        $result = $this->SendDataToDevice($endpoint, $postfields);
        return $result;
    }

    /**
     * Sets the volume of a product.
     * To set the volume of a product you must build and send a volume parameter object.
     * Set the targetvolume, 0 to 100, inclusive. Bigger is louder.
     * This does go to 11...but you may want to go a bit louder than that.
     *
     * @param int $Volume
     *
     * @return mixed
     */
    public function SetDeviceVolume(int $Volume)
    {
        $result = null;
        if ($Volume) {
            $endpoint = 'volume';
            $postfields = '<volume>' . $Volume . '</volume>';
            $result = $this->SendDataToDevice($endpoint, $postfields);
        }
        return $result;
    }

    /**
     * Selects a product's source.
     * To select local sources (AUX or Bluetooth when available) use the GET /sources endpoint to view the available sources for the product,
     * and build a Local Source ContentItem.
     * You may also select streaming sources, such as Tune-In, Pandora and Spotify.
     * For these sources, your request body should be a ContentItem taken in entirety from a previously-made /now_playing GET request.
     * Note: You need to POST the actual XML string to the product, not key-value pairings.
     *
     * @param string $Source
     * @param string $SourceAccount
     *
     * @return null
     */
    public function SelectDeviceSource(string $Source, string $SourceAccount)
    {
        $result = null;
        if ($Source && $SourceAccount) {
            $endpoint = 'select';
            $postfields = '<ContentItem source=' . $Source . ' sourceAccount=' . $SourceAccount . '></ContentItem>';
            $result = $this->SendDataToDevice($endpoint, $postfields);
        }
        return $result;
    }

    /**
     * Selects the AUX mode.
     * To select local sources (AUX or Bluetooth when available) use the GET /sources endpoint.
     * View the available sources for the product, and build a ContentItem.
     *
     * @return mixed
     */
    public function SelectDeviceAUXMode()
    {
        $endpoint = 'select';
        $postfields = '<ContentItem source=AUX sourceAccount=AUX></ContentItem>';
        $result = $this->SendDataToDevice($endpoint, $postfields);
        return $result;
    }

    /**
     * Selects the AUX mode.
     * To select local sources (AUX or Bluetooth when available) use the GET /sources endpoint.
     * View the available sources for the product, and build a ContentItem.
     *
     * @return mixed
     */
    public function SelectDeviceBluetoothMode()
    {
        $endpoint = 'select';
        $postfields = '<ContentItem source=BLUETOOTH sourceAccount=""></ContentItem>';
        $result = $this->SendDataToDevice($endpoint, $postfields);
        return $result;
    }

    /**
     * Selects HDMI_mode.
     * To select local sources (AUX or Bluetooth when available) use the GET /sources endpoint.
     * View the available sources for the product, and build a ContentItem.
     *
     * @param int $SourceNumber
     *
     * @return mixed
     */
    public function SelectDeviceHDMIMode(int $SourceNumber)
    {
        $endpoint = 'select';
        $postfields = '<ContentItem source=PRODUCT sourceAccount=HDMI_' . $SourceNumber . '></ContentItem>';
        $result = $this->SendDataToDevice($endpoint, $postfields);
        return $result;
    }

    /**
     * Selects TV mode.
     * To select local sources (AUX or Bluetooth when available) use the GET /sources endpoint.
     * View the available sources for the product, and build a ContentItem.
     *
     * @return mixed
     */
    public function SelectDeviceTVMode()
    {
        $endpoint = 'select';
        $postfields = '<ContentItem source=PRODUCT sourceAccount=TV></ContentItem>';
        $result = $this->SendDataToDevice($endpoint, $postfields);
        return $result;
    }

    /**
     * Select a radio location mode.
     * You may also select streaming sources, such as Tune-In, Pandora and Spotify.
     * For these sources, you will need a location attribute.
     * You can use GET /now_playing when a source is already in use and save this attribute to return to them later.
     * There are three (3) attributes that make up the single required ContentItem parameter object.
     *
     * @param string $Source
     * @param string $Type
     * @param string $Location
     * @param string $SourceAccount
     *
     * @return mixed
     */
    public function SelectDeviceLocation(string $Source, string $Type, string $Location, string $SourceAccount)
    {
        $endpoint = 'select';
        $postfields = '<ContentItem source="' . $Source . '" type="' . $Type . '" location="' . $Location . '" sourceAccount="' . $SourceAccount . '"></ContentItem>';
        $result = $this->SendDataToDevice($endpoint, $postfields);
        return $result;
    }

    /**
     * Creates a zone of synced products.
     * This command is used to create a zone of synced products.
     * The command must be sent to the product you wish to make the master of the zone.
     * To create a zone, you must build a zone object.
     *
     * @param string $MasterID
     * @param string $MemberIP
     * @param string $MemberID
     *
     * @return null
     */
    public function SetDeviceZone(string $MasterID, string $MemberIP, string $MemberID)
    {
        $result = null;
        if ($MasterID && $MemberIP && $MemberID) {
            $endpoint = 'setZone';
            $postfields = '<zone master=' . $MasterID . '> <member ipaddress=' . $MemberIP . '>' . $MemberID . '</member></zone>';
            $result = $this->SendDataToDevice($endpoint, $postfields);
        }
        return $result;
    }

    /**
     * Adds one or more slave product(s) to a multiroom zone.
     * This command must be sent to the master product of an existing zone.
     * Create zones using the /setZone endpoint.
     * To add a product to a zone, you must build a zone object.
     *
     * @param string $MasterID
     * @param string $MemberIP
     * @param string $MemberID
     *
     * @return null
     */
    public function AddDeviceZoneSlave(string $MasterID, string $MemberIP, string $MemberID)
    {
        $result = null;
        if ($MasterID && $MemberIP && $MemberID) {
            $endpoint = 'addZoneSlave';
            $postfields = '<zone master=' . $MasterID . '> <member ipaddress=' . $MemberIP . '>' . $MemberID . '</member></zone>';
            $result = $this->SendDataToDevice($endpoint, $postfields);
        }
        return $result;
    }

    /**
     * Removes one or more slave product(s) from a multiroom zone.
     * This command must be sent to the master product of an existing zone.
     * Create zones using the /setZone endpoint.
     * To remove a product to a zone, you must build a zone object.
     *
     * @param string $MasterID
     * @param string $MemberIP
     * @param string $MemberID
     *
     * @return null
     */
    public function RemoveDeviceZoneSlave(string $MasterID, string $MemberIP, string $MemberID)
    {
        $result = null;
        if ($MasterID && $MemberIP && $MemberID) {
            $endpoint = 'removeZoneSlave';
            $postfields = '<zone master=' . $MasterID . '> <member ipaddress=' . $MemberIP . '>' . $MemberID . '</member></zone>';
            $result = $this->SendDataToDevice($endpoint, $postfields);
        }
        return $result;
    }

    /**
     * Sets the bass level of a product, if supported.
     * Use the /bassCapabilities endpoint to find whether a given product supports bass control and,
     * if so, available ranges.
     * To change the bass level, you must build a bass object.
     *
     * @param int $Level
     *
     * @return null
     */
    public function SetDeviceBass(int $Level)
    {
        $result = null;
        if ($Level <= 0) {
            $endpoint = 'bass';
            $postfields = '<bass>' . $Level . '</bass>';
            $result = $this->SendDataToDevice($endpoint, $postfields);
        }
        return $result;
    }

    /**
     * Set the products user-facing name.
     * This endpoint allows you to change the user-facing product name, which is listed in the response
     * from /info and is displayed in the SoundTouch app.
     * To change the name, you must build a name object.
     *
     * @param string $Name
     *
     * @return null
     */
    public function SetDeviceName(string $Name)
    {
        $result = null;
        if ($Name) {
            $endpoint = 'name';
            $postfields = '<name>' . $Name . '</name>';
            $result = $this->SendDataToDevice($endpoint, $postfields);
        }
        return $result;
    }

    /**
     * Initiates playback of a specified network-accessible audio file on a SoundTouch product.
     * Only supported on the SoundTouch 10 and series III version of the SoundTouch 20, and SoundTouch 30, running firmware 14.x or higher.
     *
     * @param string $Reason
     * @param string $URL
     * @param int    $Volume
     */
    public function PlayAudioNotification(string $Reason, string $URL, int $Volume)
    {
        $endpoint = 'speaker';
        $appKey = $this->ReadPropertyString('AppKey');
        $service = $this->ReadPropertyString('ServiceDescription');
        $message = $this->ReadPropertyString('MessageDescription');
        $postfields = '<play_info><app_key>' . $appKey . '</app_key><url>' . $URL . '</url><service>' . $service . '</service><reason>' . $Reason . '</reason><message>' . $message . '</message><volume>' . $Volume . '</volume></play_info>';
        $this->SendDataToDevice($endpoint, $postfields);
    }

    /**
     * Plays an audio file from audio notification list by position number form configuration form.
     *
     * @param int $Position
     */
    public function PlayAudioNotificationFromList(int $Position)
    {
        $endpoint = 'speaker';
        $appKey = $this->ReadPropertyString('AppKey');
        $service = $this->ReadPropertyString('ServiceDescription');
        $reason = '';
        $message = $this->ReadPropertyString('MessageDescription');
        $url = '';
        $volume = 0;
        $audioNotifications = json_decode($this->ReadPropertyString('AudioNotifications'), true);
        foreach ($audioNotifications as $audioNotification) {
            if ($audioNotification['Position'] == $Position) {
                $reason = $audioNotification['Description'];
                $url = $audioNotification['URL'];
                $volume = $audioNotification['Volume'];
            }
        }
        $postfields = '<play_info><app_key>' . $appKey . '</app_key><url>' . $url . '</url><service>' . $service . '</service><reason>' . $reason . '</reason><message>' . $message . '</message><volume>' . $volume . '</volume></play_info>';
        $this->SendDataToDevice($endpoint, $postfields);
    }
}
