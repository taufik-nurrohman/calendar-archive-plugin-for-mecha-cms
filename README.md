Archive Calendar Plugin for Mecha CMS
=====================================

> Archive widget as a calendar.

This plugin requires [Calendar](http://mecha-cms.com/article/calendar-plugin "Calendar Plugin") plugin to make it work.

#### Basic Usage

~~~ .php
<?php echo Widget::calendar('archive'); ?>
~~~

#### As Widget Block

~~~ .php
<?php echo Shield::chunk('block.widget', array(
    'title' => $speak->widget->archives,
    'content' => Widget::calendar('archive')
)); ?>
~~~