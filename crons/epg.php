<?php

class Epg
{
    public $validEpg = false;
    public $epgSource;
    public $from_cache = false;
    function __construct($result, $F7b03a1f7467c01c6ea18452d9a5202f = false)
    {
        $this->eCe97C9Fe9A866e5B522e80E43B30997($result, $F7b03a1f7467c01c6ea18452d9a5202f);
    }
    public function a53d17AB9BD15890715e7947C1766953()
    {
        $output = array();
        foreach ($this->epgSource->channel as $item) {
            $channel_id = trim((string) $item->attributes()->id);
            $display_name = !empty($item->{'display-name'}) ? trim((string) $item->{'display-name'}) : '';
            if (array_key_exists($channel_id, $output)) {
                continue;
            }
            $output[$channel_id] = array();
            $output[$channel_id]['display_name'] = $display_name;
            $output[$channel_id]['langs'] = array();
        }
        foreach ($this->epgSource->programme as $item) {
            $channel_id = trim((string) $item->attributes()->channel);
            if (!array_key_exists($channel_id, $output)) {
                continue;
            }
            $title = $item->title;
            foreach ($title as $data) {
                $lang = (string) $data->attributes()->lang;
                if (!in_array($lang, $output[$channel_id]['langs'])) {
                    $output[$channel_id]['langs'][] = $lang;
                }
            }
        }
        return $output;
    }
    public function a0b90401c3241088846A84F33c2B50fF($E2b08d0d6a74fb4e054587ee7c572a9f, $streams)
    {
        global $ipTV_db;
        $f8f0da104ec866e0d96947b27214d28a = array();
        foreach ($this->epgSource->programme as $item) {
            $channel_id = (string) $item->attributes()->channel;
            if (!array_key_exists($channel_id, $streams)) {
                continue;
            }
            $ff153ef1378baba89ae1f33db3ad14bf = $Fe7c1055293ad23ed4b69b91fd845cac = '';
            $start = strtotime(strval($item->attributes()->start));
            $stop = strtotime(strval($item->attributes()->stop));
            if (empty($item->title)) {
                continue;
            }
            $title = $item->title;
            if (is_object($title)) {
                $A2b796e1bb70296d4bed8ce34ce5691b = false;
                foreach ($title as $data) {
                    if ($data->attributes()->lang == $streams[$channel_id]['epg_lang']) {
                        $A2b796e1bb70296d4bed8ce34ce5691b = true;
                        $ff153ef1378baba89ae1f33db3ad14bf = base64_encode($data);
                        break;
                    }
                }
                if (!$A2b796e1bb70296d4bed8ce34ce5691b) {
                    $ff153ef1378baba89ae1f33db3ad14bf = base64_encode($title[0]);
                }
            } else {
                $ff153ef1378baba89ae1f33db3ad14bf = base64_encode($title);
            }
            if (!empty($item->desc)) {
                $d1294148eb5638fe195478093cd6b93b = $item->desc;
                if (is_object($d1294148eb5638fe195478093cd6b93b)) {
                    $A2b796e1bb70296d4bed8ce34ce5691b = false;
                    foreach ($d1294148eb5638fe195478093cd6b93b as $d4c3c80b508f5d00d05316e7aa0858de) {
                        if ($d4c3c80b508f5d00d05316e7aa0858de->attributes()->lang == $streams[$channel_id]['epg_lang']) {
                            $A2b796e1bb70296d4bed8ce34ce5691b = true;
                            $Fe7c1055293ad23ed4b69b91fd845cac = base64_encode($d4c3c80b508f5d00d05316e7aa0858de);
                            break;
                        }
                    }
                    if (!$A2b796e1bb70296d4bed8ce34ce5691b) {
                        $Fe7c1055293ad23ed4b69b91fd845cac = base64_encode($d1294148eb5638fe195478093cd6b93b[0]);
                    }
                } else {
                    $Fe7c1055293ad23ed4b69b91fd845cac = base64_encode($item->desc);
                }
            }
            $channel_id = addslashes($channel_id);
            $streams[$channel_id]['epg_lang'] = addslashes($streams[$channel_id]['epg_lang']);
            $date_start = date('Y-m-d H:i:s', $start);
            $date_stop = date('Y-m-d H:i:s', $stop);
            $f8f0da104ec866e0d96947b27214d28a[] = '(\'' . $ipTV_db->escape($E2b08d0d6a74fb4e054587ee7c572a9f) . '\', \'' . $ipTV_db->escape($channel_id) . '\', \'' . $ipTV_db->escape($date_start) . '\', \'' . $ipTV_db->escape($date_stop) . '\', \'' . $ipTV_db->escape($streams[$channel_id]['epg_lang']) . '\', \'' . $ipTV_db->escape($ff153ef1378baba89ae1f33db3ad14bf) . '\', \'' . $ipTV_db->escape($Fe7c1055293ad23ed4b69b91fd845cac) . '\')';
        }
        return !empty($f8f0da104ec866e0d96947b27214d28a) ? $f8f0da104ec866e0d96947b27214d28a : false;
    }
    public function ece97c9FE9a866e5B522E80e43b30997($result, $F7b03a1f7467c01c6ea18452d9a5202f)
    {
        $errors = pathinfo($result, PATHINFO_EXTENSION);
        if (($errors == 'gz')) {
            $content = file_get_contents($result);
            $epgSource = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_COMPACT | LIBXML_PARSEHUGE);
            $content = gzdecode(file_get_contents($result));
            $epgSource = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_COMPACT | LIBXML_PARSEHUGE);
        }
        else if ($errors == 'xz') {
            $content = shell_exec("wget -qO- \"{$result}\" | unxz -c");
            $epgSource = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_COMPACT | LIBXML_PARSEHUGE);
        } 
        if ($epgSource !== false) {
            $this->epgSource = $epgSource;
            if (empty($this->epgSource->programme)) {
                ipTV_lib::SaveLog('Not A Valid EPG Source Specified or EPG Crashed: ' . $result);
            } else {
                $this->validEpg = true;
            }
        } else {
            ipTV_lib::SaveLog('No XML Found At: ' . $result);
        }
        $epgSource = $content = null; 
    }
}
?>
