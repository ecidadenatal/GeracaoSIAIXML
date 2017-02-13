<?php
/*
 *     E-cidade Software Publico para Gestao Municipal                
 *  Copyright (C) 2013  DBselller Servicos de Informatica             
 *                            www.dbseller.com.br                     
 *                         e-cidade@dbseller.com.br                   
 *                                                                    
 *  Este programa e software livre; voce pode redistribui-lo e/ou     
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme  
 *  publicada pela Free Software Foundation; tanto a versao 2 da      
 *  Licenca como (a seu criterio) qualquer versao mais nova.          
 *                                                                    
 *  Este programa e distribuido na expectativa de ser util, mas SEM   
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de              
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM           
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais  
 *  detalhes.                                                         
 *                                                                    
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU     
 *  junto com este programa; se nao, escreva para a Free Software     
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA          
 *  02111-1307, USA.                                                  
 *  
 *  Copia da licenca no diretorio licenca/licenca_en.txt 
 *                                licenca/licenca_pt.txt 
 */
require_once (modification("interfaces/iPadArquivoTxtBase.interface.php"));
require_once (modification("model/contabilidade/arquivos/siai/SiaiArquivoBaseXML.model.php"));
require_once (modification("libs/db_liborcamento.php"));

class SiaiArquivoXML extends SiaiArquivoBaseXML {

  protected $lGeraAnexo01;
  protected $lGeraAnexo02;
  protected $lGeraAnexo06;  
  
  /*
   * Seta se os anexos serão gerados
   */
  public function setGeraAnexo01($lGeraAnexo01) {    
    $this->lGeraAnexo01 = $lGeraAnexo01;
  }

  public function setGeraAnexo02($lGeraAnexo02) {    
    $this->lGeraAnexo02 = $lGeraAnexo02;
  }

  public function setGeraAnexo06($lGeraAnexo06) {    
    $this->lGeraAnexo06 = $lGeraAnexo06;
  }     

  /*
   * Retorna se os anexos serão gerados
   */ 
  public function getGeraAnexo01() {
    return $this->lGeraAnexo01;
  }

  public function getGeraAnexo02() {
    return $this->lGeraAnexo02;
  }

  public function getGeraAnexo06() {
    return $this->lGeraAnexo06;
  }

  /**
   * Gera o arquivo XML
   */
  public function gerarDados() {
    
    $iAnoSessao         = db_getsession('DB_anousu');
    $iInstituicaoSessao = db_getsession('DB_instit');

    $sNomeArquivo = 'contas_anuais.xml';
    $this->setNomeArquivo($sNomeArquivo);
    
    $arquivoXML = fopen("tmp/".$this->getNomeArquivo(), 'w+');
    
    //Strings para concatenar a fim de fazer o aninhamento
    $sFilhoNivel1 = " ";
    $sFilhoNivel2 = "   ";
    $sFilhoNivel3 = "     ";
    $sFilhoNivel4 = "       ";

    /******* CABEÇALHO *******/

    $sSqlCPFGestor = "SELECT cpf 
                      FROM plugins.assinaturaordenadordespesa 
                        INNER JOIN db_departorg on db01_coddepto = departamento 
                      WHERE db01_orgao = 25 
                        AND db01_unidade = 1 
                        AND db01_anousu = 2017 
                        AND principal = 't' 
                      LIMIT 1";
    $sCPFGestor    = db_utils::fieldsMemory(db_query($sSqlCPFGestor), 0)->cpf;

    $sLinhaHeader = "<remessa>";
    //Elementos filho de <remessa>
    $sLinhaCodOrgao    = $sFilhoNivel1."<codigoOrgao>{$this->getCodigoOrgaoTCE()}"; // CODIGO IDENTIFICADOR DO ORGAO
    $sLinhaCPFGestor   = $sFilhoNivel1."<cpfGestor>{$sCPFGestor}"; //CPF DO ORDENADOR DE DESPESA
    $sLinhaTipoRemessa = $sFilhoNivel1."<tipoRemessa>7"; // FIXO 7
    $sLinhaAno         = $sFilhoNivel1."<ano>{$iAnoSessao}";  // ANO DE REFERENCIA
    $sLinhaDataCriacao = $sFilhoNivel1."<dataCriacao>{$this->dtDataGeracao}";//
    $sLinhaSistemaGera = $sFilhoNivel1."<sistemaGerado>ECIDADE";// -- ECIDADE

    //Escreve no arquivo
    fputs($arquivoXML, $sLinhaHeader      ."\r\n"
                      .$sLinhaCodOrgao    ."\r\n"
                      .$sLinhaCPFGestor   ."\r\n"
                      .$sLinhaTipoRemessa ."\r\n"
                      .$sLinhaAno         ."\r\n"
                      .$sLinhaDataCriacao ."\r\n"
                      .$sLinhaSistemaGera ."\r\n");

    if($this->getGeraAnexo01()) {
      $sLinhaAnexo01 = "Gera anexo 01";      
      fputs($arquivoXML, $sLinhaAnexo01."\r\n");
    }

    if($this->getGeraAnexo02()) {
      $sLinhaAnexo02 = "Gera anexo 02";      
      fputs($arquivoXML, $sLinhaAnexo02."\r\n");
    }

    if($this->getGeraAnexo06()) {
      $sLinhaAnexo06 = "Gera anexo 06";      
      fputs($arquivoXML, $sLinhaAnexo06."\r\n");
    }

    fclose($arquivoXML);
  } 
}
