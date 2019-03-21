<?php
    /**
    * @author    JoomShaper http://www.joomshaper.com
    * @copyright Copyright (C) 2010 - 2013 JoomShaper
    * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2
    */

    // no direct access
    defined('_JEXEC') or die('Restricted access');
    
    $layout                 = $params->get('layout', 'default');
    $moduleclass_sfx        = $params->get('moduleclass_sfx');

    $force_load_fontawesome = $params->get('force_load_fontawesome');

    $document           = JFactory::getDocument();

    if ($force_load_fontawesome == 1) {
        $document->addStylesheet(JURI::base(true) . '/modules/mod_sp_features/assets/css/font-awesome.min.css');
    }
    $document->addStylesheet(JURI::base(true) . '/modules/mod_sp_features/assets/css/sp-features.css');
    // Get Column
    $columns = $params->get('columns', 4);

    $data = array();
    //Feature 1
    if( $params->get('title1') ){
        $data[0][ 'icon' ]              = $params->get('icon1');
        $data[0][ 'image' ]             = $params->get('image1');
        $data[0][ 'icon_image' ]        = $params->get('icon_image1');
        $data[0][ 'title' ]             = $params->get('title1');
        $data[0][ 'desc' ]              = $params->get('desc1');
        $data[0][ 'read_more_text' ]    = $params->get('read_more_text1');
        $data[0][ 'link' ]              = $params->get('link1');
        $data[0][ 'target' ]            = $params->get('target1');
        $data[0][ 'class' ]             = $params->get('class1');
    }

    //Feature 2
    if( $params->get('title2') ){
        $data[1][ 'icon' ]              = $params->get('icon2');
        $data[1][ 'image' ]             = $params->get('image2');
        $data[1][ 'icon_image' ]        = $params->get('icon_image2');
        $data[1][ 'title' ]             = $params->get('title2');
        $data[1][ 'desc' ]              = $params->get('desc2');
        $data[1][ 'read_more_text' ]    = $params->get('read_more_text2');
        $data[1][ 'link' ]              = $params->get('link2');
        $data[1][ 'target' ]            = $params->get('target2');
        $data[1][ 'class' ]             = $params->get('class2');
    }

    //Feature 3
    if( $params->get('title3') ){
        $data[2][ 'icon' ]              = $params->get('icon3');
        $data[2][ 'image' ]             = $params->get('image3');
        $data[2][ 'icon_image' ]        = $params->get('icon_image3');
        $data[2][ 'title' ]             = $params->get('title3');
        $data[2][ 'desc' ]              = $params->get('desc3');
        $data[2][ 'read_more_text' ]    = $params->get('read_more_text3');
        $data[2][ 'link' ]              = $params->get('link3');
        $data[2][ 'target' ]            = $params->get('target3');
        $data[2][ 'class' ]             = $params->get('class3');
    }

    //Feature 4
    if( $params->get('title4') ){
        $data[3][ 'icon' ]              = $params->get('icon4');
        $data[3][ 'image' ]             = $params->get('image4');
        $data[3][ 'icon_image' ]        = $params->get('icon_image4');
        $data[3][ 'title' ]             = $params->get('title4');
        $data[3][ 'desc' ]              = $params->get('desc4');
        $data[3][ 'read_more_text' ]    = $params->get('read_more_text4');
        $data[3][ 'link' ]              = $params->get('link4');
        $data[3][ 'target' ]            = $params->get('target4');
        $data[3][ 'class' ]             = $params->get('class4');
    }

    //Feature 5
    if( $params->get('title5') ){
        $data[4][ 'icon' ]              = $params->get('icon5');
        $data[4][ 'image' ]             = $params->get('image5');
        $data[4][ 'icon_image' ]        = $params->get('icon_image5');
        $data[4][ 'title' ]             = $params->get('title5');
        $data[4][ 'desc' ]              = $params->get('desc5');
        $data[4][ 'read_more_text' ]    = $params->get('read_more_text5');
        $data[4][ 'link' ]              = $params->get('link5');
        $data[4][ 'target' ]            = $params->get('target5');
        $data[4][ 'class' ]             = $params->get('class5');
    }

    require(JModuleHelper::getLayoutPath('mod_sp_features', $layout) );