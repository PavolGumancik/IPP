<?php

/*
* VUT - IPP - 2019/2020
*
* @author:  Pavol Gumancik
*           xguman01
*           <xguman01@stud.fit.vutbr.cz>
*/

  /*
  *@brief Check arguments
  *
  *@param $argv Arguments of program
  *@param $argc Number of arguments
  */
  function argument_check($argc, $argv) {
    if ($argc == 1) {
      return;
    }
    elseif (($argc == 2) && ($argv[1] == "--help")) {
      echo "Simple parser for languague IPPCode20.\n
            Errors:
            10 - Wrong arguments." . PHP_EOL;
      exit (0);
    }
    else {
      exit (10);
    }
  }

  /*
  *@brief process instructions
  *
  *@param Pline ukazatel na parsovanu line
  *@param order poradie prikazu
  */
  function line_processing(&$Pline, $order){
    switch ($Pline[0]) {
      case "MOVE":

          break;
      case "CREATEFRAME":

          break;
      case "PUSHFRAME":

          break;
      case "POPFRAME":

          break;
      case "DEFVAR":

          break;
      case "CALL":
        // code...
        break;
      case "RETURN":
        // code...
        break;
      case "PUSHS":
        // code...
        break;
      case "POPS":
        // code...
        break;
      case "ADD":
        // code...
        break;
      case "SUB":
        // code...
        break;
      case "MUL":
        // code...
        break;
      case "LT":
        // code...
        break;
      case "GT":
        // code...
        break;
      case "EQ":
        // code...
        break;
      case "AND":
        // code...
        break;
      case "OR":
        // code...
        break;
      case "NOT":
        // code...
        break;
      case "INT2CHAR":
        // code...
        break;
      case "STRI2INT":
        // code...
        break;
      case "READ":
        // code...
        break;
      case "WRITE":
        // code...
        break;
      case "CONCAT":
        // code...
        break;
      case "STRLEN":
        // code...
        break;
      case "GETCHAR":
        // code...
        break;
      case "SETCHAR":
        // code...
        break;
      case "TYPE":
        // code...
        break;
      case "LABEL":
        // code...
        break;
      case "JUMP":
        // code...
        break;
      case "JUMPIFEQ":
        // code...
        break;
      case "JUMPIFNEQ":
        // code...
        break;
      case "EXIT":
        // code...
        break;
      case "DPRINT":
        // code...
        break;
      case "BREAK":
        // code...
        break;
      default:
        exit(22);
    }
  }

  /*
  *@brief nacitanie vstupu aj jeho nasledne zpracovanie
  */
  function parse(){
    $order = 0;
    while($Dline = fgets(STDIN)){ // Nacitavanie riadku za riadkom
      //odstranenie komentarov
      $Dline = preg_replace('/\#(.)*/','',$Dline);
      //aplikacia regexu na odstranenie medzier
      $Dline = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $Dline);  //https://stackoverflow.com/questions/2326125/remove-multiple-whitespaces
      //odstranenie zaciatocnych a koncovych white space
      $Dline = trim($Dline);

      //echo $Dline . PHP_EOL;

      // KONTROLA VALIDITY ZACIATKU PROGRAMU
      if($order == 0) {
        // program nie je validny
        if($Dline != '.IPPcode20'){
          exit (21);
        // validita programu
        }
        else{
          $order++;
          $xml = new DOMDocument("1.0","UTF-8");
          //$xml->setAttribute("language", "IPPcode20");
          $root = $xml->createElement('program');
          $rootNode = $xml->appendChild($root);
          $rootNode->setAttribute("language", "IPPcode20"); // pridanie atributu
          continue;
        }
      }
      if (($Dline !='') && ($order > 0)){ // kontrola neprazdnosti stringu
        // rozdeli Dline do pola "Pline" na jednotlive slova
        $Pline = preg_split('/\s+/', $Dline);
        line_processing($Pline, $order);
        //echo $Dline . PHP_EOL; // print line
        $order++;
      }
    }
    //KONEC WHILE LOOPU
    echo $xml->saveXML();


  }

argument_check($argc, $argv);
parse();
?>
