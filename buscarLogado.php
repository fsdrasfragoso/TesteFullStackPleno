
<?php include('seguranca.php'); //biblioteca resposavel pela segurança da pagina ?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="TesteFullStackPleno" />
    <title>TesteFullStackPleno</title>
    <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.3.0/pure-min.css"> <!-- carrega a framework de estilo de pagina do yahoo-->
    <link rel="stylesheet" href="estilos/styles.css" /> <!--Ajuste de refinamento no CSS-->
</head>
<body>
<div class="header">
    <div class="pure-menu pure-menu-open pure-menu-fixed pure-menu-horizontal">
        <a class="pure-menu-heading" href="index.php">TesteFullStackPleno</a>
        <ul>
        <li> <form action="" method="post" > 
            <input id="id_palavra" name="palavra" type="text" size="50"/> 
            
                    <button type="submit" class="pure-button pure-button-primary">Buscar</button>
            
            </form> </li> <!--Formulario responsavel pelas buscas internas no sistemas-->
            
            <li class="pure-menu-selected"><a href="editar.php">Início</a></li>
            <li class="pure-menu-selected"><a href="editar.php">Postes</a></li>

            <li><a href="cadastroPoste.php">Postar Texto</a></li>
            
            <li><a href="?sair=true">sair</a></li>
           
        </ul> <!--resposavel por organizar o menu no topo da pagina-->
    </div>
</div>
<div class="content">
    <div class="splash">
        <div class="pure-g-r">
            <div class="pure-u-1">
                <div class="l-box splash-text">
                    <h1 class="splash-head">
                        Uma Simples Plataforma de Postagem de Textos
                    </h1>
                    <h2 class="splash-subhead">
                        O TesteFullStackPleno visa simplificar a postagem de textos na internet, provendo ferramentas objetivas e de fácil uso par o compartilhamento de texto.
                    </h2>
                    <h3> Poste seus textos favoritos e os compartilhe na rede mundila de computadores  </h3>
                    <p>
                        <a href="cadastroPoste.php" class="pure-button primary-button">Postar Texto</a>
                    </p>
                </div>
            </div>
        </div>
    </div> <!--Divulgar o plantaforma e capitar novos usuarios-->

 <?php 
            $palavra = $_POST['palavra'];
            $poste = DB::getConn()->prepare("SELECT p.id, p.title, p.slug, p.body, p.image, a.nome FROM posts p INNER JOIN authors a ON p.authors_id=a.id WHERE p.title LIKE '%".$palavra."%' OR p.slug LIKE '%".$palavra."%' OR p.body LIKE '%".$palavra."%' OR a.nome LIKE '%".$palavra."%';");
            //seleção INNER JOIN dos campos id, titulo, slug, body e imagem na tabela posts e nome na tabela autores. 
            //Essa seleção consunta poupa requisições ao banco de dados otimizando o desenpenho do sistema!
            
            $poste->execute();
            while($p = $poste->fetch(PDO::FETCH_ASSOC)){ 
            // laço de repetição responsavel por gerar a estrutura que comportará os postes obtidos pela requisição ao BD!               
             
 ?>   
    <div class="pure-g-r content-ribbon">
        <div class="pure-u-2-3">
            <div class="l-box">
                <h4 class="content-subhead"><?php echo $p['title']?></h4>
                <h3><?php echo $p['slug']?></h3>
                <p>
                <?php echo $p['body']?>
                </p>
                <h5> Autor: <?php echo $p['nome']?> </h5>
                <h6>Tags: <?php 
                 $tags = DB::getConn()->prepare("Select t.id, t.tag from posts p inner join posts_tags pq on p.id = pq.posts_id inner join tags t on t.id=pq.tags_id where p.id=?;");
                //seleção na tabela posts_tags com uma associação INNER JOIN para exibir as tags referentes ao poste
                 $tags->execute(array($p['id']));
                 while($tag = $tags->fetch(PDO::FETCH_ASSOC)){
                   //laço de repetição responsavel por exibir todas as tegs do poste
                    echo ' <a class="pure-menu-heading" href="tag.php?id='.$tag['id'].'">'.$tag['tag'].'</a> / '; 
                 }
                 echo '</h6>';
                ?> 
                 <h6> <a type="submit" class="button-warning pure-button" href="editaposte.php?id=<?php echo $p['id'] ?>">Editar</a> / <a type="submit" class="button-error pure-button" href="apagar.php?id=<?php echo $p['id'] ?>">Apagar</a>   </h6>
            </div>
        </div>
        <?php if($p['image'] != '0'){ ?>
        <div class="pure-u-1-3">
            <div class="l-box">
                <img src="<?php echo $p['image']?>"
                     alt="<?php echo $p['title']?>">
                   
            </div>
        </div>
        <?php }?>
    </div>
    <?php }?> 
    <?php 
         $busca_tag = DB::getConn()->prepare("Select p.id, p.title, p.slug, p.body, p.image, a.nome from posts p inner join posts_tags pq on p.id = pq.posts_id inner join tags t on t.id=pq.tags_id inner join authors a ON p.authors_id=a.id where t.tag=?;");
         //seleção conjunta das tabelas que se relacionam com a tabela posts, fazendo uma filtragem por tag.
         //Essa seleção tem o objetivo de exibir os posts que tem como tag a palavra digitada no form buscar.
         $busca_tag->execute(array($palavra));
         while($busca_post = $busca_tag->fetch(PDO::FETCH_ASSOC)){
          //laço de repetição gerar a estrura para os posts recebidos pela requisição ao banco!    
         
    ?>
    <div class="pure-g-r content-ribbon">
    <?php if($busca_post['image'] != '0'){ 
        //if verifica se existe uma imagem vinculada ao poste. Caso exista ela será exibida na estrura abaixo ?>
    
        <div class="pure-u-1-3">
            <div class="l-box">
                <img src="<?php echo $busca_post['image']?>"
                     alt="<?php echo $busca_post['title']?>">
            </div>
        </div>
        <?php }?>
        <div class="pure-u-2-3">
            <div class="l-box">
                <h4 class="content-subhead"><?php echo $busca_post['title']?></h4>
                <h3><?php echo $busca_post['slug']?></h3>
                <p>
                <?php echo $busca_post['body']?>
                </p>
                <h5> Autor: <?php echo $busca_post['nome']?> </h5>
                <h6>Tags: <?php 
                 $Tags = DB::getConn()->prepare("Select t.id, t.tag from posts p inner join posts_tags pq on p.id = pq.posts_id inner join tags t on t.id=pq.tags_id where p.id=?;");
                 $Tags->execute(array($busca_post['id']));
                 while($Tag = $Tags->fetch(PDO::FETCH_ASSOC)){
                     echo ' <a class="pure-menu-heading" href="tag.php?id='.$Tag['id'].'">'.$Tag['tag'].'</a> / '; 
                 }
                 echo '</h6>';
                ?> 
            </div>
        </div>
    </div> 
    <?php }?>  

   
    <div class="footer">
    TesteFullStackPleno - Uma simples plataforma de postagem de texto
    </div> 
</div>
<script src="http://yui.yahooapis.com/3.12.0/build/yui/yui-min.js"></script>
</body>
</html>