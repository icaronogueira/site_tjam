<?php
    /**
    * @author    JoomShaper http://www.joomshaper.com
    * @copyright Copyright (C) 2010 - 2013 JoomShaper
    * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2
    */

	// no direct access
    defined('_JEXEC') or die;
    
    ?>

    <div class="sp-features <?php echo $moduleclass_sfx;?>">

    <?php foreach(array_chunk($data, $columns) as $data) { ?>
        <div class="row-fluid">

            <?php foreach ($data as $value): ?>
                <?php
                    $class = (isset($value['class']) && ($value['class'] !='')) ? $value['class'] : '' ;
                ?>
                <div class="span<?php echo round(12/$columns); ?> <?php echo $class; ?>">
                    <div class="sp-feature">
                            <?php if ($value['icon_image']== 1): ?>
                                <div class="feature-img-wrapper">
                                    <?php if( isset($value['link']) && ($value['link'] !='') ): ?>
                                    <a target="<?php echo $value['target']; ?>" href="<?php echo $value['link']; ?>">
                                        <img src="<?php echo $value['image']; ?>" />
                                    </a>
                                    <?php else: ?>
                                        <img src="<?php echo $value['image']; ?>" />
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <?php if( isset($value['link']) && ($value['link'] !='') ): ?>
                                <a target="<?php echo $value['target']; ?>" href="<?php echo $value['link']; ?>">
                                    <i class="fa <?php echo $value['icon']; ?>"></i>
                                </a>
                                <?php else: ?>
                                    <i class="fa <?php echo $value['icon']; ?>"></i>
                                <?php endif; ?>
                            <?php endif ?>

                        <?php if( isset($value['title']) && ($value['title'] !='') ): ?>
                            <?php if( isset($value['link']) && ($value['link'] !='') ): ?>
                             <a target="<?php echo $value['target']; ?>" href="<?php echo $value['link']; ?>">
                                <h3><?php echo $value['title']; ?></h3>
                            </a>
                            <?php else: ?>
                            <h3><?php echo $value['title']; ?></h3>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if( isset($value['desc']) && ($value['desc'] !='') ): ?>
                            <p><?php echo $value['desc']; ?></p>
                        <?php endif; ?>
                        <?php if( isset($value['read_more_text']) && ($value['read_more_text'] !='') && ($value['link'] !='')): ?>
                            <a target="<?php echo $value['target']; ?>" href="<?php echo $value['link']; ?>"><?php echo $value['read_more_text']; ?></a>
                        <?php endif; ?>
                    </div><!--/.sp-feature-->
                </div>
                <?php endforeach; ?>
        </div><!--/.row-fluid-->

        <?php } // END:: array_chunk ?>

    </div><!--/.sp-features-->