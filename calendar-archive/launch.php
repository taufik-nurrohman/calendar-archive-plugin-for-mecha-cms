<?php

// Make sure all plugins already loaded ...
Weapon::add('plugins_after', function() use($config, $speak) {
    Calendar::hook('archive', function($lot, $year, $month, $id) use($config, $speak) {
        $the_year = Calendar::year($id, $year);
        $the_month = Calendar::month($id, $month);
        // Load data if the calendar time is equal to current time
        if($the_year === $year && $the_month === $month) {
            $month_str = $month < 10 ? '0' . $month : (string) $month;
            if($files = Get::articles('DESC', 'time:' . $year . '-' . $month_str)) {
                $months = (array) $speak->month_names;
                // link to archive page
                $lot[$year . '/' . $month] = array(
                    'url' => $config->url . '/' . $config->archive->slug . '/' . $year . '-' . $month_str,
                    'description' => sprintf($config->archive->title, $year . ', ' . $months[$month - 1])
                );
                $lot_o = array();
                foreach($files as $file) {
                    $post = Get::articleAnchor($file);
                    list($time, $kind, $slug) = explode('_', File::N($file), 3);
                    $s = explode('-', $time);
                    // link to article page by default
                    $lot_o[$year . '/' . $month . '/' . (int) $s[2]][] = array(
                        'url' => $post->url,
                        'description' => $post->title,
                        'kind' => (array) $post->kind
                    );
                }
                foreach($lot_o as $k => $v) {
                    // more than 1 article in a day, link to archive page
                    if(count($v) > 1) {
                        $s = array();
                        foreach($v as $vv) {
                            $s[] = $vv['description'];
                        }
                        $lot[$k]['url'] = $lot[$year . '/' . $month]['url'];
                        $lot[$k]['title'] = '%d+'; // add a plus sign
                        $lot[$k]['description'] = implode(', ', $s);
                    // else, link to article page
                    } else {
                        $lot[$k] = $v[0];
                    }
                }
                unset($lot_o, $files);
            }
        }
        // Replace default calendar navigation URL with archive page URL ...
        $y_p = $lot['prev']['year'];
        $m_p = $lot['prev']['month'];
        $y_n = $lot['next']['year'];
        $m_n = $lot['next']['month'];
        if($m_p < 10) $m_p = '0' . $m_p;
        if($m_n < 10) $m_n = '0' . $m_n;
        $lot['prev']['url'] = $config->url . '/' . $config->archive->slug . '/' . $y_p . '-' . $m_p;
        $lot['next']['url'] = $config->url . '/' . $config->archive->slug . '/' . $y_n . '-' . $m_n;
        return $lot;
    });
});

// Hijack HTTP query of calendar based on `$config->archive_query` value ...
Weapon::add('shield_lot_before', function() use($config) {
    if($query = Config::get('archive_query')) {
        $s = explode('-', $query . '-' . date('m'));
        $ss = Calendar::$config['query'];
        $_GET[$ss]['archive']['year'] = (int) $s[0];
        $_GET[$ss]['archive']['month'] = (int) $s[1];
    }
});