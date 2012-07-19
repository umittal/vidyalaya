<?php

$libDir = realpath(dirname(__FILE__));
require_once "$libDir/vidyalaya.inc";

class OpeningDay {
  private static function DistributionMaterialFamilyHtml($family, $year) {
    $templateDir = realpath(dirname(__FILE__)."/../templates");
    $pageheader = '<a href=""><img src="http://www.vidyalaya.us/modx/assets/templates/vidyalaya/images/Vheader2.jpg" width="700" height="70" alt="php5 logo"/></a>';
    $timestamp = date('d M Y h:i A');
    $footer = '<div id="footer"> <table><tr><td>Copyright &#169; 2011 Vidyalya Inc., Printed at: ' 
      . $timestamp 
      . '</td><td style="text-align:right;"><div class="page-number"></div></td></tr></table></div>';

    // Header 
    $template = new HTML_Template_ITX($templateDir);
    $template->loadTemplatefile("Layout.tpl", true, true);
    $template->addBlockFile('TOP', 'F_TOP', 'LayoutTop.tpl');
    $template->touchBlock('F_TOP');
    $html = $template->get();

    // Family Detail Form - one per family
    $template = new HTML_Template_ITX($templateDir);
    $template->loadTemplatefile("Layout.tpl", true, true);

    $template->addBlockFile('CONTENT', 'F_CONTENT', 'LayoutContent.tpl');
    $template->touchBlock('F_CONTENT');
    $template->setCurrentBlock('HEADER');
    $template->setVariable("HEADER", $pageheader);
    $template->parseCurrentBlock();

    $template->addBlockFile('RESULT', 'F_RESULT', 'FamilyDetail.tpl');
    $template->touchBlock('F_RESULT');
    DisplayFamilyTemplateV3($template, $family);
    $html = $html . $template->get();	

    foreach ($family->EligibleChildren() as $student) {
      $html = $html . $footer . '<DIV style="page-break-after:always"></DIV>';
      $template = new HTML_Template_ITX($templateDir);
      $template->loadTemplatefile("Layout.tpl", true, true);
      $template->addBlockFile('CONTENT', 'F_CONTENT', 'LayoutContent.tpl');
      $template->touchBlock('F_CONTENT');
      $template->setCurrentBlock('HEADER');
      $template->setVariable("HEADER", $pageheader);
      $template->parseCurrentBlock();

      DisplayStudent($template, $student);


      $html = $html . $template->get();			
      $html = $html . $footer . '<DIV style="page-break-after:always"></DIV>';
      $html .= "<img src='/home/umesh/Dropbox/Vidyalaya-Roster/2011-12/Layouts/PHHSLayout.jpg' width='632' height='700' alt='layout'>\n";
    }
    $html = $html . $footer ;
    return $html;
  }
									  

  public static function PrintDistributionMaterialFamily($family, $year) {
    if (empty($family)) return;

    $session=Calendar::SessionFromYear($year);

    $html = self::DistributionMaterialFamilyHtml($family, $year);
    $pdf = PrintFactory::HtmlToPdf($html);
    
    $printDir = "/home/umesh/Dropbox/Vidyalaya-Roster/$session/OpeningDay";

    $fileName = $printDir . "/pdf/Family-" . $family->id . ".pdf";
    file_put_contents("$fileName", $pdf);
    echo "printed $fileName\n";
    return;

    $fileName = $printDir . "/html/Family-" . $family->id . ".html";
    file_put_contents("$fileName", $html);
    echo "printed $fileName\n";
    return;
  }


  public static function PrintDistributionMaterial () {
    $year = Calendar::CurrentYear();
    $i=1;
    foreach (FamilyTracker::GetRegisteredFamiliesYear($year) as $tracker) {
      echo $i++ . ". Family $tracker->family, printing \n";
      self::PrintDistributionMaterialFamily(Family::GetItemById($tracker->family), $year);
    }
  }

  public static function RunAll() {
  }

}
?>

