<?php

namespace App\Utilities;

class Constants
{
    //Queues
    const QUEUE_BOX_SCAN_GF_SUBMIT = 'boxscanitemsubmit';

    //Box-Build
    const BOX_BUILD_LABEL_VID_MIX = 'MIX';
    const BOX_BUILD_DIRECT_SCAN_CENTER = 'boxBuildDirectScanCenter';
    const BOX_BUILD_DIRECT_SCAN_STATUS = 'boxBuildDirectScanStatus';

    //Scan

    const NO_SN_VALUE = 'No S/N';

    // Scan statuses

    const SCAN_STATUS_NEW = 'new';
    const SCAN_STATUS_DONE = 'done';
    const SCAN_STATUS_CONFIRMED = 'confirmed';

    //Settings

    const SETTINGS_CHECK_REQUIRED_LIST = 'checkRequiredList';
    const SETTINGS_RAZER_API_STATUS = 'razerAPIStatus';
    const NEW_ITEM_SCAN_LOCK_STATUS = 'newItemScanLockStatus';

    //scan audios

    const AUDIO_FILE_PATH = 'public/sounds/audio/';
    const AUDIO_BATTERY_SCRAP_SETTINGS = 'scanBatteryScrapText';
    const AUDIO_SCRAP_SETTINGS = 'scanScrapText';
    const AUDIO_ERROR_SETTINGS = 'audioError';
    const STRING_FOR_REQUIRED_LIST_AUDIO_NAME = 'requiredList';
    const STRING_FOR_BOX_BUILD_AUDIO_NAME = 'boxBuild';

    //user settings
    const TEST_AUDIO_ASSIGNMENT = 'testAudio';


    //scrap pallet types
    const SCRAP_PALLET_TYPE_RAZER = 'RAZER';
    const SCRAP_PALLET_TYPE_SCRAP = 'TRASH';


    //goodsflow
    const GOODSFLOW_CONDITIONS_PREFIX_ARRAY = ['_A', '_B', '_C', '_D', '_U', '_X', '_R', '_S'];

    //gates
    const GATE_PHONE_NUMBER = 'gatePhoneNumber';

    //Look over
    const AUTO_LOOK_OVER_STATUS = 'autoLookOverStatus';


}
