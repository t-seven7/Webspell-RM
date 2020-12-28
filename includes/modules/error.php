<?php
/*-----------------------------------------------------------------\
| _    _  ___  ___  ___  ___  ___  __    __      ___   __  __       |
|( \/\/ )(  _)(  ,)/ __)(  ,\(  _)(  )  (  )    (  ,) (  \/  )      |
| \    /  ) _) ) ,\\__ \ ) _/ ) _) )(__  )(__    )  \  )    (       |
|  \/\/  (___)(___/(___/(_)  (___)(____)(____)  (_)\_)(_/\/\_)      |
|                       ___          ___                            |
|                      |__ \        / _ \                           |
|                         ) |      | | | |                          |
|                        / /       | | | |                          |
|                       / /_   _   | |_| |                          |
|                      |____| (_)   \___/                           |
\___________________________________________________________________/
/                                                                   \
|        Copyright 2005-2018 by webspell.org / webspell.info        |
|        Copyright 2018-2019 by webspell-rm.de                      |
|                                                                   |
|        - Script runs under the GNU GENERAL PUBLIC LICENCE         |
|        - It's NOT allowed to remove this copyright-tag            |
|        - http://www.fsf.org/licensing/licenses/gpl.html           |
|                                                                   |
|               Code based on WebSPELL Clanpackage                  |
|                 (Michael Gruber - webspell.at)                    |
\___________________________________________________________________/
/                                                                   \
|                     WEBSPELL RM Version 2.0                       |
|           For Support, Mods and the Full Script visit             |
|                       webspell-rm.de                              |
\------------------------------------------------------------------*/

if (isset($_GET['type'])) {
    $type = $_GET['type'];
} else {
    $type = null;
}

$_language->readModule('error');

if ($type == 404) {
    $error_header = $_language->module['error_404'];
    $error_message = $_language->module['message_404'];
}

if (isset($error_header)) {
    echo '<h2>' . $error_header . '</h2>';
    echo $error_message;
} else {
    echo '<h2>Error</h2>';
}

$urlparts = preg_split('/[\s.,-\/]+/si', $_GET['url']);
$results = array();
foreach ($urlparts as $tag) {
    $sql = safe_query("SELECT * FROM " . PREFIX . "tags WHERE tag='" . $tag . "'");
    if ($sql->num_rows) {
        while ($ds = mysqli_fetch_assoc($sql)) {
            $data_check = null;
            if ($ds['rel'] == "news") {
                $data_check = \webspell\Tags::getNews($ds['ID']);
            } elseif ($ds['rel'] == "articles") {
                $data_check = \webspell\Tags::getArticle($ds['ID']);
            } elseif ($ds['rel'] == "static") {
                $data_check = \webspell\Tags::getStaticPage($ds['ID']);
            } elseif ($ds['rel'] == "faq") {
                $data_check = \webspell\Tags::getFaq($ds['ID']);
            }
            if (is_array($data_check)) {
                $results[] = $data_check;
            }
        }
    }
}
if (count($results)) {
    echo "<h1>" . $_language->module['alternative_results'] . "</h1>";
    usort($results, array('Tags', 'sortByDate'));
    echo "<p class='text-center'><strong>" . count($data) . "</strong> " . $_language->module['results_found'] . "</p>";
    foreach ($results as $entry) {
        $date = getformatdate($entry['date']);
        $type = $entry['type'];
        $auszug = $entry['content'];
        $link = $entry['link'];
        $title = $entry['title'];
        $data_array = array();
        $data_array['$date'] = $date;
        $data_array['$link'] = $link;
        $data_array['$title'] = $title;
        $data_array['$auszug'] = $auszug;
        $search_tags = $GLOBALS["_template"]->replaceTemplate("search_tags", $data_array);
        echo $search_tags;
    }
}
