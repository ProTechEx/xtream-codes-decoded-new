<?php
/*Rev:26.09.18r0*/

if (!@$argc) {
    die(0);
}
require str_replace('\\', '/', dirname($argv[0])) . '/../wwwdir/init.php';
$unique_id = TMP_DIR . md5(UniqueID() . __FILE__);
KillProcessCmd($unique_id);
cli_set_process_title('XtreamCodes[VOD CC Checker]');
ini_set('memory_limit', -1);
$ipTV_db->query('SELECT * FROM `streams` t1 INNER JOIN `transcoding_profiles` t2 ON t2.profile_id = t1.transcode_profile_id WHERE t1.type = 3');
if (0 < $ipTV_db->num_rows()) {
    $streams = $ipTV_db->get_rows();
    foreach ($streams as $stream) {
        echo '[*] Checking Stream ' . $stream['stream_display_name'] . '';
        switch (ipTV_stream::TranscodeBuild($stream['id'])) {
            case 1:
                echo 'Build Is Still Going!';
                ipTV_stream::TranscodeBuild($stream['id']);
                break;
            case 2:
                echo 'Build Finished';
                break;
        }
    }
}
$pid = ipTV_servers::getPidFromProcessName(SERVER_ID, FFMPEG_PATH);
$ipTV_db->query('SELECT t1.*,t2.* FROM `streams_sys` t1 INNER JOIN `streams` t2 ON t2.id = t1.stream_id AND t2.direct_source = 0 INNER JOIN `streams_types` t3 ON t3.type_id = t2.type AND t3.live = 0 WHERE (t1.to_analyze = 1 OR t1.stream_status = 2) AND t1.server_id = \'%d\'', SERVER_ID);
if (0 < $ipTV_db->num_rows()) {
    $series_data = $ipTV_db->get_rows();
    foreach ($series_data as $data) {
        echo '[*] Checking Movie ' . $data['stream_display_name'] . ' ON Server ID ' . $data['server_id'] . ' 		---> ';
        if ($data['to_analyze'] == 1) {
            if (!empty($pid[$data['server_id']]) && in_array($data['pid'], $pid[$data['server_id']])) {
                echo 'WORKING';
            } else {
                echo '';
                $target_container = json_decode($data['target_container'], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $data['target_container'] = $target_container;
                } else {
                    $data['target_container'] = array($data['target_container']);
                }
                $data['target_container'] = $data['target_container'][0];
                $ed147a39fb35be93248b6f1c206a8023 = MOVIES_PATH . $data['stream_id'] . '.' . $data['target_container'];
                if ($Ec610f8d82d35339f680a3ec9bbc078c = ipTV_stream::e0a1164567005185e0818F081674e240($ed147a39fb35be93248b6f1c206a8023, $data['server_id'])) {
                    $duration = isset($Ec610f8d82d35339f680a3ec9bbc078c['duration']) ? $Ec610f8d82d35339f680a3ec9bbc078c['duration'] : 0;
                    sscanf($duration, '%d:%d:%d', $fd8f2c4ad459c3f2b875636e5d3ac6a7, $Bc1d36e0762a7ca0e7cbaddd76686790, $Ba3faa92a82fb2d1bb6bb866cb272fee);
                    $duration_secs = isset($Ba3faa92a82fb2d1bb6bb866cb272fee) ? $fd8f2c4ad459c3f2b875636e5d3ac6a7 * 3600 + $Bc1d36e0762a7ca0e7cbaddd76686790 * 60 + $Ba3faa92a82fb2d1bb6bb866cb272fee : $fd8f2c4ad459c3f2b875636e5d3ac6a7 * 60 + $Bc1d36e0762a7ca0e7cbaddd76686790;
                    $Ff876e96994aa5b09ce92e771efe2038 = ipTV_servers::F320b6a3920944D8a18d7949C8aBaCe4($data['server_id'], 'wc -c < ' . $ed147a39fb35be93248b6f1c206a8023, 'raw');
                    $D2f61e797d44efa20d9d559b2fc2c039 = round($Ff876e96994aa5b09ce92e771efe2038[$data['server_id']] * 0.008 / $duration_secs);
                    $movie_propeties = json_decode($data['movie_propeties'], true);
                    if (!is_array($movie_propeties)) {
                        $movie_propeties = array();
                    }
                    if (!isset($movie_propeties['duration_secs']) || $duration_secs != $movie_propeties['duration_secs']) {
                        $movie_propeties['duration_secs'] = $duration_secs;
                        $movie_propeties['duration'] = $duration;
                    }
                    if (!isset($movie_propeties['video']) || $Ec610f8d82d35339f680a3ec9bbc078c['codecs']['video']['codec_name'] != $movie_propeties['video']) {
                        $movie_propeties['video'] = $Ec610f8d82d35339f680a3ec9bbc078c['codecs']['video'];
                    }
                    if (!isset($movie_propeties['audio']) || $Ec610f8d82d35339f680a3ec9bbc078c['codecs']['audio']['codec_name'] != $movie_propeties['audio']) {
                        $movie_propeties['audio'] = $Ec610f8d82d35339f680a3ec9bbc078c['codecs']['audio'];
                    }
                    if (!isset($movie_propeties['bitrate']) || $D2f61e797d44efa20d9d559b2fc2c039 != $movie_propeties['bitrate']) {
                        $movie_propeties['bitrate'] = $D2f61e797d44efa20d9d559b2fc2c039;
                    }
                    $ipTV_db->query('UPDATE `streams` SET `movie_propeties` = \'%s\' WHERE `id` = \'%d\'', json_encode($movie_propeties), $data['stream_id']);
                    $ipTV_db->query('UPDATE `streams_sys` SET `bitrate` = \'%d\',`to_analyze` = 0,`stream_status` = 0,`stream_info` = \'%s\'  WHERE `server_stream_id` = \'%d\'', $D2f61e797d44efa20d9d559b2fc2c039, json_encode($Ec610f8d82d35339f680a3ec9bbc078c), $data['server_stream_id']);
                    echo 'VALID';
                } else {
                    $ipTV_db->query('UPDATE `streams_sys` SET `to_analyze` = 0,`stream_status` = 1  WHERE `server_stream_id` = \'%d\'', $data['server_stream_id']);
                    echo 'BAD MOVIE';
                }
            }
        } else {
            echo 'NO ACTION';
        }
    }
}
?>
