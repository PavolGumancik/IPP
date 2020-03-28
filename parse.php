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

  $xml = new DOMDocument("1.0","UTF-8");
  $root = $xml->createElement('program');
  $xml->formatOutput=true;

  $xml->appendChild($root);
  $root->setAttribute("language", "IPPcode20"); // pridanie atributu

  /*
  *@brief Chybny kod, ak nie je label
  *
  *@param counter n-ty prvok pola
  *@param Pline parsovany riadok
  */
  function exit_no_label($counter, &$Pline){
    if (preg_match('/int@/', $Pline[$counter]) === 1) {
      exit (23);
    }
    if (preg_match('/bool@/', $Pline[$counter]) === 1) {
      exit (23);
    }
    if (preg_match('/nil@/', $Pline[$counter]) === 1) {
      exit (23);
    }
    if (preg_match('/type@/', $Pline[$counter]) === 1) {
      exit (23);
    }
    if (preg_match('/GF@/', $Pline[$counter]) === 1) {
      exit (23);
    }
    if (preg_match('/LF@/', $Pline[$counter]) === 1) {
      exit (23);
    }
    if (preg_match('/TF@/', $Pline[$counter]) === 1) {
      exit (23);
    }
  }

  /*
  *@brief Vrati type premennej
  *
  *@param counter n-ty prvok pola
  *@param Pline parsovany riadok
  */
  function veriable($counter, &$Pline){
    $Avariable=preg_split('#(?<!\\\)\@#',$Pline[$counter]);
    if ($Avariable[0] != 'GF' || $Avariable[0] != 'LF' || $Avariable[0] != 'TF') {
      return ($Avariable[1]);
    }
    else {
      return $Pline[$counter];
    }
  }

  /*
  *@brief uprava prvkov do xml
  *
  *@param counter x-ty prvok pola, ktory upravujem
  *@param Pline, ukazatel na pole obsahujuce aktualny prikaz
  */
  function argXML($counter, &$Pline){

    $Avariable=preg_split('#(?<!\\\)\@#',$Pline[$counter]); // rozdelenie do pola podla @
    //print_r( $Avariable);
    switch ($Avariable[0]) {
      case "int":
        return 'int';
        break;
      case 'bool':
        return 'bool';
        break;
      case 'string':
        return 'string';
        break;
      case 'nil':
        return '';
      case 'label':
        return 'label';
        break;
      case 'type':
        return '';
        break;
      case 'GF':
        return 'var';
        break;
      case 'LF':
        return 'var';
        break;
      case 'TF':
        return 'var';
        break;
      default:
        exit (23);
        break;
    }

  }

  /*
  *@brief vrati obsah premennej
  *
  *@param counter x-ty prvok pola, ktory upravujem
  *@param Pline, ukazatel na pole obsahujuce aktualny prikaz
  */
  function contain($counter, $variable){
    $Avariable=preg_split('#(?<!\\\)\@#',$variable); // parsovanie podla @
    $countArr = count($Avariable); // pocet prvkov pola

    if ($Avariable[0] == 'GF' || $Avariable[0] == 'LF' || $Avariable[0] == 'TF') { // vrati format XF@premenna
      return $variable;
    }
    else {
      if ($countArr == 1){ // prazdna premenna
        return '';
      }
      else {
        if ($countArr > 1) { // vrati premennu
          return $Avariable[1];
        }
        else {
          exit(23);
        }
      }
    }
  }

  /*
  *@brief kontrola argumentu
  *
  *@param argc pocet argumentu
  *@param argv pole argumentu
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
  *@brief process Zaradenie instrukci a nasledovne vytvorenie elementu
  *
  *@param Pline ukazatel na parsovanu line
  *@param order poradie prikazu
  */
  function line_processing(&$Pline, $order){
    global $xml,$root;

    switch ($Pline[0]) {
      case "MOVE":
          if (count($Pline) != 3) {
            exit (23);
          }
          // vytvorenie a spojenie
          $instruct = $xml->createElement('instruction');
          $root->appendChild($instruct);

          //prirazeni atributu k elementu
          $instruct->setAttribute("order", $order);
          $instruct->setAttribute("opcode", "MOVE");

          //vytvorenie "child nodu" a jeho spojeni s korenovou instrukci
          $argument = $xml->createElement('arg1',contain($order, $Pline[1]));
          $instruct->appendChild($argument);
          $argument->setAttribute("type",argXML(1,$Pline));

          //vytvorenie "child nodu" a jeho spojeni s korenovou instrukci
          $argumen_t = $xml->createElement('arg2',contain($order, $Pline[2]));
          $instruct->appendChild($argumen_t);
          $argumen_t->setAttribute("type",argXML(2,$Pline));
          break;
      case "CREATEFRAME":
          if (count($Pline) != 1) {
            exit (23);
          }
          $instruct = $xml->createElement('instruction');
          $root->appendChild($instruct);
          $instruct->setAttribute("order", $order);
          $instruct->setAttribute("opcode", "CREATEFRAME");
          break;
      case "PUSHFRAME":
          if (count($Pline) != 1) {
            exit (23);
          }
          $instruct = $xml->createElement('instruction');
          $root->appendChild($instruct);
          $instruct->setAttribute("order", $order);
          $instruct->setAttribute("opcode", "PUSHFRAME");
          break;
      case "POPFRAME":
          if (count($Pline) != 1) {
            exit (23);
          }
          $instruct = $xml->createElement('instruction');
          $root->appendChild($instruct);
          $instruct->setAttribute("order", $order);
          $instruct->setAttribute("opcode", "POPFRAME");
          break;
      //opakovaná definice proměnné jižexistující v daném rámci vede na chybu 52.
      case "DEFVAR":
          if (count($Pline) != 2) {

            exit (23);
          }
          if (argXML(1, $Pline) != 'var') {
            exit (23);
          }
          $instruct = $xml->createElement('instruction');
          $root->appendChild($instruct);

          $instruct->setAttribute("order", $order);
          $instruct->setAttribute("opcode", "DEFVAR");

          $argument = $xml->createElement('arg1',contain($order, $Pline[1]));
          $instruct->appendChild($argument);
          $argument->setAttribute("type",argXML(1,$Pline));

          break;
      case "CALL":
        if (count($Pline) != 2) {
          exit (23);
        }
        $instruct = $xml->createElement('instruction');
        $root->appendChild($instruct);

        $instruct->setAttribute("order", $order);
        $instruct->setAttribute("opcode", "CALL");

        $argument = $xml->createElement('arg1', $Pline[1]);
        $instruct->appendChild($argument);
        $argument->setAttribute("type","label");
        break;
      case "RETURN":
        if (count($Pline) != 1) {
          exit (23);
        }
        $instruct = $xml->createElement('instruction');
        $root->appendChild($instruct);

        $instruct->setAttribute("order", $order);
        $instruct->setAttribute("opcode", "RETURN");
        break;
      case "PUSHS":
        if (count($Pline) != 2) {

          exit (23);
        }
        $instruct = $xml->createElement('instruction');
        $root->appendChild($instruct);

        $instruct->setAttribute("order", $order);
        $instruct->setAttribute("opcode", "PUSHS");

        $argument = $xml->createElement('arg1', $Pline[1]);
        $instruct->appendChild($argument);
        $argument->setAttribute("type",argXML(1,$Pline)); //
        break;
      case "POPS":
        if (count($Pline) != 2) {

          exit (23);
        }
        if (argXML(1, $Pline) != 'var') {
          exit (23);
        }
        $instruct = $xml->createElement('instruction');
        $root->appendChild($instruct);

        $instruct->setAttribute("order", $order);
        $instruct->setAttribute("opcode", "POPS");

        $argument = $xml->createElement('arg1', $Pline[1]);
        $instruct->appendChild($argument);
        $argument->setAttribute("type",argXML(1,$Pline));
        break;
      case "ADD":
        if (count($Pline) != 4) {

          exit (23);
        }
        if (argXML(1, $Pline) != 'var') {
          exit (23);
        }
        $instruct = $xml->createElement('instruction');
        $root->appendChild($instruct);

        $instruct->setAttribute("order", $order);
        $instruct->setAttribute("opcode", "ADD");

        $argument = $xml->createElement('arg1', $Pline[1]);
        $instruct->appendChild($argument);
        $argument->setAttribute("type",argXML(1,$Pline));

        $argumentt = $xml->createElement('arg2',contain($order, $Pline[2]));
        $instruct->appendChild($argumentt);
        $argumentt->setAttribute("type",argXML(2,$Pline));

        $argumen_t = $xml->createElement('arg3',contain($order, $Pline[3]));
        $instruct->appendChild($argumen_t);
        $argumen_t->setAttribute("type",argXML(3,$Pline));

        break;
      case "SUB":
        if (count($Pline) != 4) {

          exit (23);
        }
        if (argXML(1, $Pline) != 'var') {
          exit (23);
        }
        $instruct = $xml->createElement('instruction');
        $root->appendChild($instruct);

        $instruct->setAttribute("order", $order);
        $instruct->setAttribute("opcode", "SUB");

        $argument = $xml->createElement('arg1', $Pline[1]);
        $instruct->appendChild($argument);
        $argument->setAttribute("type",argXML(1,$Pline));

        $argumentt = $xml->createElement('arg2',contain($order, $Pline[2]));
        $instruct->appendChild($argumentt);
        $argumentt->setAttribute("type",argXML(2,$Pline));

        $argumen_t = $xml->createElement('arg3',contain($order, $Pline[3]));
        $instruct->appendChild($argumen_t);
        $argumen_t->setAttribute("type",argXML(3,$Pline));
        break;
      case "MUL":
        if (count($Pline) != 4) {

          exit (23);
        }
        if (argXML(1, $Pline) != 'var') {
          exit (23);
        }
        $instruct = $xml->createElement('instruction');
        $root->appendChild($instruct);

        $instruct->setAttribute("order", $order);
        $instruct->setAttribute("opcode", "MUL");

        $argument = $xml->createElement('arg1', $Pline[1]);
        $instruct->appendChild($argument);
        $argument->setAttribute("type",argXML(1,$Pline));

        $argumentt = $xml->createElement('arg2',contain($order, $Pline[2]));
        $instruct->appendChild($argumentt);
        $argumentt->setAttribute("type",argXML(2,$Pline));

        $argumen_t = $xml->createElement('arg3',contain($order, $Pline[3]));
        $instruct->appendChild($argumen_t);
        $argumen_t->setAttribute("type",argXML(3,$Pline));
        break;
      case "IDIV":
        if (count($Pline) != 4) {
          exit (23);
        }
        if (argXML(1, $Pline) != 'var') {
          exit (23);
        }
        $instruct = $xml->createElement('instruction');
        $root->appendChild($instruct);

        $instruct->setAttribute("order", $order);
        $instruct->setAttribute("opcode", "IDIV");

        $argument = $xml->createElement('arg1', $Pline[1]);
        $instruct->appendChild($argument);
        $argument->setAttribute("type",argXML(1,$Pline));

        $argumentt = $xml->createElement('arg2',contain($order, $Pline[2]));
        $instruct->appendChild($argumentt);
        $argumentt->setAttribute("type",argXML(2,$Pline));

        $argumen_t = $xml->createElement('arg3',contain($order, $Pline[3]));
        $instruct->appendChild($argumen_t);
        $argumen_t->setAttribute("type",argXML(3,$Pline));
        break;
      case "LT":
        if (count($Pline) != 4) {

          exit (23);
        }
        if (argXML(1, $Pline) != 'var') {
          exit (23);
        }
        $instruct = $xml->createElement('instruction');
        $root->appendChild($instruct);

        $instruct->setAttribute("order", $order);
        $instruct->setAttribute("opcode", "LT");

        $argument = $xml->createElement('arg1', $Pline[1]);
        $instruct->appendChild($argument);
        $argument->setAttribute("type",argXML(1,$Pline));

        $argumentt = $xml->createElement('arg2',contain($order, $Pline[2]));
        $instruct->appendChild($argumentt);
        $argumentt->setAttribute("type",argXML(2,$Pline));

        $argumen_t = $xml->createElement('arg3',contain($order, $Pline[3]));
        $instruct->appendChild($argumen_t);
        $argumen_t->setAttribute("type",argXML(3,$Pline));
        break;
      case "GT":
        if (count($Pline) != 4) {

          exit (23);
        }
        if (argXML(1, $Pline) != 'var') {
          exit (23);
        }
        $instruct = $xml->createElement('instruction');
        $root->appendChild($instruct);

        $instruct->setAttribute("order", $order);
        $instruct->setAttribute("opcode", "GT");

        $argument = $xml->createElement('arg1', $Pline[1]);
        $instruct->appendChild($argument);
        $argument->setAttribute("type",argXML(1,$Pline));

        $argumentt = $xml->createElement('arg2',contain($order, $Pline[2]));
        $instruct->appendChild($argumentt);
        $argumentt->setAttribute("type",argXML(2,$Pline));

        $argumen_t = $xml->createElement('arg3',contain($order, $Pline[3]));
        $instruct->appendChild($argumen_t);
        $argumen_t->setAttribute("type",argXML(3,$Pline));
        break;
      case "EQ":
        if (count($Pline) != 4) {

          exit (23);
        }
        if (argXML(1, $Pline) != 'var') {
          exit (23);
        }
        $instruct = $xml->createElement('instruction');
        $root->appendChild($instruct);

        $instruct->setAttribute("order", $order);
        $instruct->setAttribute("opcode", "EQ");

        $argument = $xml->createElement('arg1', $Pline[1]);
        $instruct->appendChild($argument);
        $argument->setAttribute("type",argXML(1,$Pline));

        $argumentt = $xml->createElement('arg2',contain($order, $Pline[2]));
        $instruct->appendChild($argumentt);
        $argumentt->setAttribute("type",argXML(2,$Pline));

        $argumen_t = $xml->createElement('arg3',contain($order, $Pline[3]));
        $instruct->appendChild($argumen_t);
        $argumen_t->setAttribute("type",argXML(3,$Pline));
        break;
      case "AND":
        if (count($Pline) != 4) {

          exit (23);
        }
        if (argXML(1, $Pline) != 'var') {
          exit (23);
        }
        $instruct = $xml->createElement('instruction');
        $root->appendChild($instruct);

        $instruct->setAttribute("order", $order);
        $instruct->setAttribute("opcode", "AND");

        $argument = $xml->createElement('arg1', $Pline[1]);
        $instruct->appendChild($argument);
        $argument->setAttribute("type",argXML(1,$Pline));

        $argumentt = $xml->createElement('arg2',contain($order, $Pline[2]));
        $instruct->appendChild($argumentt);
        $argumentt->setAttribute("type",argXML(2,$Pline));

        $argumen_t = $xml->createElement('arg3',contain($order, $Pline[3]));
        $instruct->appendChild($argumen_t);
        $argumen_t->setAttribute("type",argXML(3,$Pline));
        break;
      case "OR":
        if (count($Pline) != 4) {

          exit (23);
        }
        if (argXML(1, $Pline) != 'var') {
          exit (23);
        }
        $instruct = $xml->createElement('instruction');
        $root->appendChild($instruct);

        $instruct->setAttribute("order", $order);
        $instruct->setAttribute("opcode", "OR");

        $argument = $xml->createElement('arg1', $Pline[1]);
        $instruct->appendChild($argument);
        $argument->setAttribute("type",argXML(1,$Pline));

        $argumentt = $xml->createElement('arg2',contain($order, $Pline[2]));
        $instruct->appendChild($argumentt);
        $argumentt->setAttribute("type",argXML(2,$Pline));

        $argumen_t = $xml->createElement('arg3',contain($order, $Pline[3]));
        $instruct->appendChild($argumen_t);
        $argumen_t->setAttribute("type",argXML(3,$Pline));
        break;
      case "NOT":
        if (count($Pline) != 4) {

          exit (23);
        }
        if (argXML(1, $Pline) != 'var') {
          exit (23);
        }
        $instruct = $xml->createElement('instruction');
        $root->appendChild($instruct);

        $instruct->setAttribute("order", $order);
        $instruct->setAttribute("opcode", "NOT");

        $argument = $xml->createElement('arg1', $Pline[1]);
        $instruct->appendChild($argument);
        $argument->setAttribute("type",argXML(1,$Pline));

        $argumentt = $xml->createElement('arg2',contain($order, $Pline[2]));
        $instruct->appendChild($argumentt);
        $argumentt->setAttribute("type",argXML(2,$Pline));

        $argumen_t = $xml->createElement('arg3',contain($order, $Pline[3]));
        $instruct->appendChild($argumen_t);
        $argumen_t->setAttribute("type",argXML(3,$Pline));
        break;
      case "INT2CHAR":
        if (count($Pline) != 3) {

          exit (23);
        }
        if (argXML(1, $Pline) != 'var') {
          exit (23);
        }
        $instruct = $xml->createElement('instruction');
        $root->appendChild($instruct);

        $instruct->setAttribute("order", $order);
        $instruct->setAttribute("opcode", "INT2CHAR");

        $argument = $xml->createElement('arg1', $Pline[1]);
        $instruct->appendChild($argument);
        $argument->setAttribute("type",argXML(1,$Pline));

        $argumentt = $xml->createElement('arg2',contain($order, $Pline[2]));
        $instruct->appendChild($argumentt);
        $argumentt->setAttribute("type",argXML(2,$Pline));
        break;
      case "STRI2INT":
        if (count($Pline) != 4) {

          exit (23);
        }
        if (argXML(1, $Pline) != 'var') {
          exit (23);
        }
        $instruct = $xml->createElement('instruction');
        $root->appendChild($instruct);

        $instruct->setAttribute("order", $order);
        $instruct->setAttribute("opcode", "STRI2INT");

        $argument = $xml->createElement('arg1', $Pline[1]);
        $instruct->appendChild($argument);
        $argument->setAttribute("type",argXML(1,$Pline));

        $argumentt = $xml->createElement('arg2',contain($order, $Pline[2]));
        $instruct->appendChild($argumentt);
        $argumentt->setAttribute("type",argXML(2,$Pline));

        $argumen_t = $xml->createElement('arg3',contain($order, $Pline[3]));
        $instruct->appendChild($argumen_t);
        $argumen_t->setAttribute("type",argXML(3,$Pline));
        break;
      case "READ":
        if (count($Pline) != 3) {

          exit (23);
        }
        if (argXML(1, $Pline) != 'var') {
          exit (23);
        }
        $instruct = $xml->createElement('instruction');
        $root->appendChild($instruct);

        $instruct->setAttribute("order", $order);
        $instruct->setAttribute("opcode", "READ");

        $argument = $xml->createElement('arg1', $Pline[1]);
        $instruct->appendChild($argument);
        $argument->setAttribute("type",argXML(1,$Pline));

        $argumentt = $xml->createElement('arg2',contain($order, $Pline[2]));
        $instruct->appendChild($argumentt);
        $argumentt->setAttribute("type",argXML(2,$Pline));
        break;
      case "WRITE":
        if (count($Pline) != 2) {
          exit (23);
        }
        // vytvorenie a spojenie
        $instruct = $xml->createElement('instruction');
        $root->appendChild($instruct);

        $instruct->setAttribute("order", $order);
        $instruct->setAttribute("opcode", "WRITE");

        $argument = $xml->createElement('arg1',contain($order, $Pline[1]));
        $instruct->appendChild($argument);
        $argument->setAttribute("type",argXML(1,$Pline));
        break;
      case "CONCAT":
        if (count($Pline) != 4) {

          exit (23);
        }
        if (argXML(1, $Pline) != 'var') {
          exit (23);
        }
        $instruct = $xml->createElement('instruction');
        $root->appendChild($instruct);

        $instruct->setAttribute("order", $order);
        $instruct->setAttribute("opcode", "CONCAT");

        $argument = $xml->createElement('arg1', $Pline[1]);
        $instruct->appendChild($argument);
        $argument->setAttribute("type",argXML(1,$Pline));

        $argumentt = $xml->createElement('arg2',contain($order, $Pline[2]));
        $instruct->appendChild($argumentt);
        $argumentt->setAttribute("type",argXML(2,$Pline));

        $argumen_t = $xml->createElement('arg3',contain($order, $Pline[3]));
        $instruct->appendChild($argumen_t);
        $argumen_t->setAttribute("type",argXML(3,$Pline));
        break;
      case "STRLEN":
        if (count($Pline) != 3) {

          exit (23);
        }
        if (argXML(1, $Pline) != 'var') {
          exit (23);
        }
        $instruct = $xml->createElement('instruction');
        $root->appendChild($instruct);

        $instruct->setAttribute("order", $order);
        $instruct->setAttribute("opcode", "STRLEN");

        $argument = $xml->createElement('arg1', $Pline[1]);
        $instruct->appendChild($argument);
        $argument->setAttribute("type",argXML(1,$Pline));

        $argumentt = $xml->createElement('arg2',contain($order, $Pline[2]));
        $instruct->appendChild($argumentt);
        $argumentt->setAttribute("type",argXML(2,$Pline));
        break;
      case "GETCHAR":
        if (count($Pline) != 4) {

          exit (23);
        }
        if (argXML(1, $Pline) != 'var') {
          exit (23);
        }
        $instruct = $xml->createElement('instruction');
        $root->appendChild($instruct);

        $instruct->setAttribute("order", $order);
        $instruct->setAttribute("opcode", "GETCHAR");

        $argument = $xml->createElement('arg1', $Pline[1]);
        $instruct->appendChild($argument);
        $argument->setAttribute("type",argXML(1,$Pline));

        $argumentt = $xml->createElement('arg2',contain($order, $Pline[2]));
        $instruct->appendChild($argumentt);
        $argumentt->setAttribute("type",argXML(2,$Pline));

        $argumen_t = $xml->createElement('arg3',contain($order, $Pline[3]));
        $instruct->appendChild($argumen_t);
        $argumen_t->setAttribute("type",argXML(3,$Pline));
        break;
      case "SETCHAR":
        if (count($Pline) != 4) {

          exit (23);
        }
        if (argXML(1, $Pline) != 'var') {
          exit (23);
        }
        $instruct = $xml->createElement('instruction');
        $root->appendChild($instruct);

        $instruct->setAttribute("order", $order);
        $instruct->setAttribute("opcode", "SETCHAR");

        $argument = $xml->createElement('arg1', $Pline[1]);
        $instruct->appendChild($argument);
        $argument->setAttribute("type",argXML(1,$Pline));

        $argumentt = $xml->createElement('arg2',contain($order, $Pline[2]));
        $instruct->appendChild($argumentt);
        $argumentt->setAttribute("type",argXML(2,$Pline));

        $argumen_t = $xml->createElement('arg3',contain($order, $Pline[3]));
        $instruct->appendChild($argumen_t);
        $argumen_t->setAttribute("type",argXML(3,$Pline));
        break;
      case "TYPE":
        if (count($Pline) != 3) {
          exit (23);
        }
        if (argXML(1, $Pline) != 'var') {
          exit (23);
        }
        $instruct = $xml->createElement('instruction');
        $root->appendChild($instruct);

        $instruct->setAttribute("order", $order);
        $instruct->setAttribute("opcode", "TYPE");

        $argument = $xml->createElement('arg1', $Pline[1]);
        $instruct->appendChild($argument);
        $argument->setAttribute("type",argXML(1,$Pline));
        break;
      //Pokus o redefinici existujícího návěští je chybou 52.
      case "LABEL":
        if (count($Pline) != 2) {
          exit (23);
        }
        $instruct = $xml->createElement('instruction');
        $root->appendChild($instruct);

        $instruct->setAttribute("order", $order);
        $instruct->setAttribute("opcode", "LABEL");

        $argument = $xml->createElement('arg1', $Pline[1]);
        $instruct->appendChild($argument);
        $argument->setAttribute("type","label");
        break;
      case "JUMP":
        if (count($Pline) != 2) {
          exit (23);
        }
        exit_no_label(1, $Pline);
        $instruct = $xml->createElement('instruction');
        $root->appendChild($instruct);

        $instruct->setAttribute("order", $order);
        $instruct->setAttribute("opcode", "JUMP");

        $argument = $xml->createElement('arg1', $Pline[1]);
        $instruct->appendChild($argument);
        $argument->setAttribute("type","label");
        break;
      case "JUMPIFEQ":
        if (count($Pline) != 4) {
          exit (23);
        }
        exit_no_label(1, $Pline);
        $instruct = $xml->createElement('instruction');
        $root->appendChild($instruct);

        $instruct->setAttribute("order", $order);
        $instruct->setAttribute("opcode", "JUMPIFEQ");

        $argument = $xml->createElement('arg1', $Pline[1]);
        $instruct->appendChild($argument);
        $argument->setAttribute("type","label");

        $argumentt = $xml->createElement('arg2',contain($order, $Pline[2]));
        $instruct->appendChild($argumentt);
        $argumentt->setAttribute("type",argXML(2,$Pline));

        $argumen_t = $xml->createElement('arg3',contain($order, $Pline[3]));
        $instruct->appendChild($argumen_t);
        $argumen_t->setAttribute("type",argXML(3,$Pline));
        break;
      case "JUMPIFNEQ":
        if (count($Pline) != 4) {
          exit (23);
        }
        exit_no_label(1, $Pline);
        $instruct = $xml->createElement('instruction');
        $root->appendChild($instruct);

        $instruct->setAttribute("order", $order);
        $instruct->setAttribute("opcode", "JUMPIFEQ");

        $argument = $xml->createElement('arg1', $Pline[1]);
        $instruct->appendChild($argument);
        $argument->setAttribute("type","label");

        $argumentt = $xml->createElement('arg2',contain($order, $Pline[2]));
        $instruct->appendChild($argumentt);
        $argumentt->setAttribute("type",argXML(2,$Pline));

        $argumen_t = $xml->createElement('arg3',contain($order, $Pline[3]));
        $instruct->appendChild($argumen_t);
        $argumen_t->setAttribute("type",argXML(3,$Pline));
        break;
      case "EXIT": //SKONTROLOVAT
        if (count($Pline) != 2) {
        exit (23);
        }
        $instruct = $xml->createElement('instruction');
        $root->appendChild($instruct);

        $instruct->setAttribute("order", $order);
        $instruct->setAttribute("opcode", "EXIT");

        $argument = $xml->createElement('arg1', $Pline[1]);
        $instruct->appendChild($argument);
        $argument->setAttribute("type","int");
        break;
      case "DPRINT":
        if (count($Pline) != 2) {
          exit (23);
        }
        $instruct = $xml->createElement('instruction');
        $root->appendChild($instruct);

        $instruct->setAttribute("order", $order);
        $instruct->setAttribute("opcode", "DPRINT");

        $argument = $xml->createElement('arg1', $Pline[1]);
        $instruct->appendChild($argument);
        $argument->setAttribute("type",argXML(1,$Pline));
        break;
      case "BREAK":
        $instruct = $xml->createElement('instruction');
        $root->appendChild($instruct);

        $instruct->setAttribute("order", $order);
        $instruct->setAttribute("opcode", "BREAK");
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
    $header = False;  //kontrola havicky
    $orderOP = 1; // cislo operace

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
          if ($Dline == '') { // prazdny riadok, preskocim
            continue;
          } else {
            exit (21);
          }
        // validita programu
        }
        else{
          if ($Dline == '.IPPcode20') {
            $header = True;
          }
          $order++;
          continue;
        }
      }
      if (($Dline !='') && ($header == True)){ // kontrola neprazdnosti stringu ## bolo -(($Dline !='') && ($order > 0))
        // rozdeli Dline do pola "Pline" na jednotlive slova
        $Pline = preg_split('/\s+/', $Dline);
        line_processing($Pline, $orderOP);
        $orderOP++;
        //echo $Dline . PHP_EOL; // print line
        $order++;
      }
    }
    //KONEC WHILE LOOPU
    if ($header == False) {
      exit(21);
    }
  }

argument_check($argc, $argv);
parse();
echo $xml->saveXML();
?>
