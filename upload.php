<?php

class upload{

	private $tam_max = 2048;
	private $larg_max;
	private $altu_max;
	private $arquivo_diretorio ="arquivos/";
	private $foto_diretorio = "fotos/";
	private $extensao_arquivo;
	private $erros = array();

	private $name = "";
	private $type = "";
	private $tmp_name = "";
	private $error = "";
	private $size = "";

	/**
	*Metodo que faz o upload, recebe o array $_FILES['field']. � obrigat�rio
	*o primeiro campo do array.
	*/
	function fazer_upload($files){
		
		if(!isset($files)){
			$this->setErros("Sem arquivo selecionado");			
			return false;
		}

		//inicializa o array $files
		$this->inicializa($files);

		//verifica se � arquivo pdf ou doc
		if($this->verificaArquivo($this->type)){
			//verifica se o arquivo foi enviado, se nao retorna o valor falso
			if(!$this->verificaUpload()){
				return false;
			}

			//transforma o tamanho do arquivo em KB
			if($this->size > 0){
				$this->size = round($this->size/1024,2);
			}

			//compara se o tamanho do arquivo � maior que o permitido
			if($this->size > $this->tam_max){
				$this->setErros("O arquivo tem que ter no m�ximo 2048 KB (2M)");
				return false;
			}
			
			//seta a extens�o do arquivo
			$this->setExtensaoArquivo();
			
			//cria um nome �nico para o arquivo
			$this->setNomeArquivo();
			
			//o caminho do arquivo
			$this->arquivo_diretorio .= $this->name;

			//faz o upload da imagem
			if(!move_uploaded_file($this->tmp_name, $this->arquivo_diretorio)){
				$this->setErros("Erro durante o envio do arquivo");
			}

			return true;	
			
		}else			
		if($this->verificaFoto($this->type)){ //se o arquivo for uma imagem

			//verifica se o arquivo foi enviado, se nao retorna o valor falso
			if(!$this->verificaUpload()){
				return false;
			}

			//transforma o tamanho do arquivo em KB
			if($this->size > 0){
				$this->size = round($this->size/1024,2);
			}

			//compara se o tamanho do arquivo � maior que o permitido
			if($this->size > $this->tam_max){
				$this->setErros("O arquivo tem que ter no m�ximo 2048 KB (2M)");
				return false;
			}
			
			//seta a extens�o do arquivo
			$this->setExtensaoArquivo();
			
			//cria um nome �nico para o arquivo
			$this->setNomeArquivo();
			
			//o caminho do arquivo
			$this->foto_diretorio .= $this->name;

			//faz o upload da imagem
			if(!move_uploaded_file($this->tmp_name, $this->foto_diretorio)){
				$this->setErros("Erro durante o envio do arquivo");
			}

			return true;

		}else{
			$this->setErros("arquivo no  formato inv�lido");
			$this->foto_diretorio .= "semFoto.png";
		}

	}

	function inicializa($files){
		$this->name = $files['name'];
		$this->type = $files['type'];
		$this->tmp_name = $files['tmp_name'];
		$this->error = $files['error'];
		$this->size = $files['size'];
	}

	function setExtensaoArquivo(){
		$ext = explode(".",$this->name);
		$this->extensao_arquivo = $ext[1];
	}

	function setNomeArquivo(){
		 $temp = substr(md5(uniqid(time())), 0, 10);
		 $this->name = $temp.".".$this->extensao_arquivo;
		 if(file_exists($this->arquivo_diretorio.$this->name)){
			 $this->setNomeArquivo();
		 }
	}

	function getNomeArquivo(){
		return $this->nome_arquivo;
	}

	function getCaminho(){
		return $this->arquivo_diretorio;
	}

	function getFotoCaminho(){
		return $this->foto_diretorio;
	}

	/**
	* Fun��o que verifica se o arquivo est� em um formato v�lido
	*/
	function verificaArquivo($arquivo){
		if(!eregi("^application\/(msword|pdf)$", $arquivo)){
			return false;
		}else{
			return true;
		}
	}

	
	function verificaFoto($arquivo){
		if(!eregi("^image\/(pjpeg|jpeg|png|gif|bmp)$", $arquivo)){
			return false;
		}else{
			return true;
		}

	}

	function setErros($erro){
		$this->erros[] = $erro;
	}

	function getErros(){
		$inicio = "<p class\"erro\">";
		$final = "</p>";
		$str = "";

		foreach($this->erros as $val){
			$str .= $inicio.$val.$final;
		}
		return $str;
	}

	function verificaUpload(){
		if(!is_uploaded_file($this->tmp_name)){
			if($this->error == 1){
				$this->setErros("Tamanho do arquivo � maior que o permitido");
			}else
			if($this->error == 2){
				$this->setErros("Tamanho do arquivo � maior que o permitido (no formul�rio)");
			}else
			if($this->error == 3){
				$this->setErros("O arquivo foi enviado parcialmente");
			}else
			if($this->error == 4){
				$this->setErros("O arquivo n�o foi enviado!");
			}else		
			if($this->error == 6){
				$this->setErros("Arquivo sem diret�rio tempor�rio");
			}else
			if($this->error == 7){
				$this->setErros("N�o conseguiu ler o arquivo");
			}else
			if($this->error == 8){
				$this->setErros("Arquivo parado pela extens�o");
			}
			return false;
		}
		return true;
	}
}

?>