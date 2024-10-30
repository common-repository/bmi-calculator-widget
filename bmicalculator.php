<?php
/*
Plugin Name: BMI Calculator Widget
Plugin URI: http://www.bmicalculator.org
Description: A BMI Calculator Widget
Version: 1.3   
Author: Domain Superstar LLC
Author URI: http://www.domainsuperstar.com
*/
?>
<?
add_action("widgets_init", array('BMI_Calculator_Widget', 'register'));
register_activation_hook( __FILE__, array('BMI_Calculator_Widget', 'activate'));
register_deactivation_hook( __FILE__, array('BMI_Calculator_Widget', 'deactivate'));


add_action('init', 'add_bmi_javascript');

function add_bmi_javascript()
{
   if ( is_admin() )
   {
      wp_enqueue_script ('calc-colorpicker', WP_PLUGIN_URL . '/bmi-calculator-widget/js/plugins/colorpicker/colorpicker.js', array('jquery'));
      wp_enqueue_script ('calcs', WP_PLUGIN_URL . '/bmi-calculator-widget/js/calcs.js', array('jquery'));
      wp_enqueue_style('colorpicker-styles', WP_PLUGIN_URL . '/bmi-calculator-widget/js/plugins/colorpicker/css/colorpicker.css');
      wp_enqueue_style('calc-styles', WP_PLUGIN_URL . '/bmi-calculator-widget/css/calcs.css');
   }
   else
   {
      wp_enqueue_script ('jquery');
   }
}

class BMI_Calculator_Widget {
   function activate()
   {
      $data = array( 'title' => 'BMI Calculator' ,'standard' => 'standard', 'allowLink'=>'yes');
      if ( !get_option('BMI_Calculator_Widget')){
         add_option('BMI_Calculator_Widget' , $data);
      } else {
        update_option('BMI_Calculator_Widget' , $data);
      }
   }

   function deactivate(){
      delete_option('BMI_Calculator_Widget');
   }

   function control(){
      $data = get_option('BMI_Calculator_Widget');
   ?>
     <p><label>Title<input name="title" type="text" value="<?php echo $data['title']; ?>" /></label></p>
     <p><label>Calculator Mode<br/>
           Standard<input type="radio" name="standard" type="text" value="standard" <?php echo $data['standard'] == "standard" ? "checked" : "" ?>/>
           Metric<input type="radio" name="standard" type="text" value="metric" <?php echo $data['standard'] =="metric" ? "checked" : "" ?>/>
     </label></p>
     <div class="colorHolder"><div class="colorSelector" id="bgColorSelector"><div id="widgetBackground" style="background-color: <?php echo $data['bgcolor']; ?>"></div></div> <span>Widget Start Background Color</span> </div>
     <div class="colorHolder"><div class="colorSelector" id="bgEndColorSelector"><div id="widgetEndBackground" style="background-color: <?php echo $data['bgendcolor']; ?>"></div></div> <span>Widget End Background Color</span> </div>
     <div class="colorHolder"><div class="colorSelector" id="textColorSelector"><div id="widgetText" style="background-color: <?php echo $data['textcolor']; ?>"></div></div> <span>Widget Text Color</span></div>
     <input name="bgcolor" type="hidden" value="<?php echo $data['bgcolor']; ?>" /></label>
     <input name="bgendcolor" type="hidden" value="<?php echo $data['bgendcolor']; ?>" />
     <input name="textcolor" type="hidden" value="<?php echo $data['textcolor']; ?>" />
     <div id="bmiCalcDemo" style="color: <?php echo $data['textcolor']; ?>; border: 1px solid rgba(21, 11, 11, 0.199219); padding: 5px; width: 200px; -moz-border-radius: 12px; -webkit-border-radius: 12px; border-radius: 12px; -moz-box-shadow: 0px 0px 4px #ffffff; -webkit-box-shadow: 0px 0px 4px #ffffff; box-shadow: 0px 0px 4px #ffffff; background-color: <?php echo $data['bgcolor']; ?>; background-image: -moz-linear-gradient(top, <?php echo $data['bgcolor']; ?>, <?php echo $data['bgendcolor']; ?>); background-image: -webkit-gradient(linear,left top,left bottom,color-stop(0, <?php echo $data['bgcolor']; ?>),color-stop(1, <?php echo $data['bgendcolor']; ?>)); filter:  progid:DXImageTransform.Microsoft.gradient(startColorStr='<?php echo $data['bgcolor']; ?>', EndColorStr='<?php echo $data['bgendcolor']; ?>'); -ms-filter: \"progid:DXImageTransform.Microsoft.gradient(startColorStr='<?php echo $data['bgcolor']; ?>', EndColorStr='<?php echo $data['bgendcolor']; ?>')\"; text-shadow: 1px 1px 3px #888;">
        Widget Color Preview
     </div>
     <p><label>Allow link back to BMICalculator.org<input name="allowLink" type="checkbox" value="yes" <?= $data['allowLink'] == 'yes' ? "checked" : "" ?>/></label></p>
     <?php
      if (isset($_POST['title'])){
       $data['title'] = attribute_escape($_POST['title']);
       $data['standard'] = attribute_escape($_POST['standard']);
       $data['textcolor'] = attribute_escape($_POST['textcolor']);
       $data['bgcolor'] = attribute_escape($_POST['bgcolor']);
       $data['bgendcolor'] = attribute_escape($_POST['bgendcolor']);
       $data['allowLink'] = attribute_escape($_POST['allowLink']);
       update_option('BMI_Calculator_Widget', $data);
     }
  }
  function widget($args){
           extract( $args );
           $options = get_option('BMI_Calculator_Widget', $data);
           extract($options);
           ?>
                 <?php echo $before_widget; ?>
      <script>
         jQuery(document).ready(function($) {
            $("#bmisubmit").click(function()
             {
               var bmi = 0;
               var height = 0;
               var weight = 0;

               <? if($standard == 'standard') { ?>
                  height = $("[name='bmifeet']").val() * 12 + parseInt($("[name='bmiinches']").val());
                  weight = $("[name='bmipounds']").val() * 703;
               <? } else { ?>
                  height = $("[name='bmifeet']").val() / 100;
                  weight = $("[name='bmipounds']").val();
               <? } ?>

               bmi = weight / Math.pow(height, 2);

               bmi = Math.round(bmi * 10) / 10;

               if(bmi < 12)
               {
                  desiredCat = "severly underweight";
               }
               else if (bmi < 18.5)
               {
                  desiredCat = "underweight";
               }
               else if (bmi < 25)
               {
                  desiredCat = "normal";
               }
               else if (bmi < 30)
               {
                  desiredCat = "overweight";
               }
               else if (bmi < 35)
               {
                  desiredCat = "moderately obese";
               }
               else
               {
                  desiredCat = "severly obese";
               }

               if(isNaN(bmi))
                  $('#BMIresults').hide().text('Please enter a valid height and weight.').fadeIn();
               else
                  $('#BMIresults').hide().text('Your BMI is ' + bmi + ". This puts you in the " + desiredCat + " weight range.").fadeIn();

             });
          });
      </script>
      <table style="color: <?= $textcolor ? $textcolor : "#ffffff" ?>; padding: 5px; margin: 0; width: 200px; font-size: 9pt; -moz-border-radius: 12px; -webkit-border-radius: 12px; border-radius: 12px; -moz-box-shadow: 0px 0px 4px #ffffff; -webkit-box-shadow: 0px 0px 4px #ffffff; box-shadow: 0px 0px 4px #ffffff; background-color: <?php echo $bgcolor ? $bgcolor : '#3399CC' ?>; background-image: -moz-linear-gradient(top, <?php echo $bgcolor ? $bgcolor : '#3399CC' ?>, <?php echo $bgendcolor ? $bgendcolor : '#1C5992' ?>); background-image: -webkit-gradient(linear,left top,left bottom,color-stop(0, <?php echo $bgcolor ? $bgcolor : '#3399CC' ?>),color-stop(1, <?php echo $bgendcolor ? $bgendcolor : '#1C5992' ?>)); filter:  progid:DXImageTransform.Microsoft.gradient(startColorStr='<?php echo $bgcolor ? $bgcolor : '#3399CC' ?>', EndColorStr='<?php echo $bgendcolor ? $bgendcolor : '#1C5992' ?>'); -ms-filter: \"progid:DXImageTransform.Microsoft.gradient(startColorStr='<?php echo $bgcolor ? $bgcolor : '#3399CC' ?>', EndColorStr='<?php echo $bgendcolor ? $bgendcolor : '#1C5992' ?>')\"; text-shadow: 1px 1px 3px #888;" id="bmiTable">
         <tbody>
            <tr><td colspan="2" align="center"><h4 style="color: <?= $textcolor ? $textcolor : "#ffffff" ?>; margin: 0; padding: 3px;"><?= $title ?></h4></td></tr>
         <? if($standard == 'standard') { ?>
            <tr><td style="padding: 3px;">Height: </td><td><input name="bmifeet" style="width: 30px;"> ft <input name="bmiinches" style="width: 30px;"> in</td></tr>
            <tr><td style="padding: 3px;">Weight: </td><td><input name="bmipounds" style="width: 77px;"> pounds</td></tr>
         <? } else { ?>
            <tr><td style="padding: 3px;">Height: </td><td><input name="bmifeet" style="width: 30px;"> cm </td></tr>
            <tr><td style="padding: 3px;">Weight: </td><td><input name="bmipounds" style="width: 77px;"> kg</td></tr>
         <? } ?>
         <tr><td colspan="2" style="padding: 3px;" align="center"><input id="bmisubmit" type="button" value="Get BMI!" style="cursor: pointer; border: 0; padding: 5px; color:<?= $bgcolor ? $bgcolor : "#1C5992" ?>; background-color: <?= $textcolor ? $textcolor  : "#ffffff" ?>; font-weight: bold;"></td></tr>
         <tr><td colspan="2" style="padding: 3px;"><div id="BMIresults" style="font-weight: bold;"></div></td></tr>
         <? if($allowLink == 'yes') { ?>
            <tr><td colspan="2" align="center"><a href="http://www.bmicalculator.org/" style="text-decoration: underline; color: <?= $textcolor ? $textcolor : "#ffffff" ?>;">BMI Calculator</a></td></tr>
         <? } ?>
      </tbody></table>
                 <?php echo $after_widget; ?>
           <?php
	}

  function register(){
    register_sidebar_widget('BMI Calculator', array('BMI_Calculator_Widget', 'widget'));
    register_widget_control('BMI Calculator', array('BMI_Calculator_Widget', 'control'));
  }
}
?>