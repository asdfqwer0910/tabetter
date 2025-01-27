<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css" integrity="sha256-5uKiXEwbaQh9cgd2/5Vp6WmMnsUr3VZZw0a8rKnOKNU=" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/Bar4.css?<?php echo date('YmdHis'); ?>"/>
    <link rel="stylesheet" href="../css/Oyamadatime2.css?<?php echo date('YmdHis'); ?>"/>
    <link rel="stylesheet" type="text/css" href="../css/modal.css?<?php echo date('YmdHis'); ?>"/>
    <link rel="stylesheet" href="../css/Oyamadaprofile.css?<?php echo date('YmdHis'); ?>"/>
    <link rel="stylesheet" href="../css/scrollable.css?<?php echo date('YmdHis'); ?>"/>
</head>
<body>
    <?php
        session_start();
        require_once '../DAO/postdb.php';
        require_once '../DAO/userdb.php';
        require_once '../DAO/T.shosaidb.php';
        require_once '../DAO/search.php';
        $daoPostDb = new DAO_post();
        $daoUserDb = new DAO_userdb();
        $daoTshosaiDb = new DAO_Tshosaidb();
        $daoSearch = new DAO_search();

        // ユーザーアイコンのSQL
        $pdo = new PDO('mysql:host=localhost; dbname=tabetterdb; charset=utf8',
        'webuser', 'abccsd2');

        $sql2 = "SELECT * FROM user_image WHERE user_id = ? ";
        $stmt2 = $pdo->prepare($sql2);
        $stmt2->bindValue(1, $_SESSION['user_id'], PDO::PARAM_STR);
        $stmt2->execute();
        $image2 = $stmt2->fetch(PDO::FETCH_ASSOC);

        $img2 = base64_encode($image2['user_image']);
    ?>
    <div id="app">
    <!-- ヘッダー -->
   <header class="border-bottom" id="header">
    <div class="container-fluid">
        <div class="row row justify-content-between">
            <div class="d-flex align-items-center mb-0 text-dark text-decoration-none col-7 text-left px-0" style="height: 50px; padding-top: 55px;">
            <img src="../svg/a.svg">
            </div>
    
            <button class="navbar-toggler col-3 p-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample05" aria-controls="navbarsExample05" aria-expanded="false" aria-label="Toggle navigation" style="height: 50px; box-shadow: none;">
                <img src="../svg/b.svg" width="50" height="50" viewBox="0 0 60 60" fill="none" > 
            </button>
        </div>
        <div class="collapse navbar-collapse" id="navbarsExample05">
            <form id="search" wtx-context="0C9FB6AB-0B58-4B25-A43A-44B7ADC851E5" action="timeLine.php" class="mx-4" method="get">
              <input class="form-control text-center mb-3" type="text" name="key" placeholder="キーワードを入力" aria-label="Search" wtx-context="AA84657A-0F9B-4A04-B5FA-D24659B477FD"
              style="height: 34px;
              border: 3px solid #FFAC4A; 
              box-shadow: none;" required>
              <input type="submit" style="height:50px; width:50px;" form="search">
            </form>
        </div>
    </div>
  </header>
  <!-- ヘッダー↑ -->

  <div class="scrollable">

  <?php 
if(!empty($_GET['key'])){
?>

<div class="container-fluid">
    <div class="row">

    <?php
        $searchPostIds = array();
        $searchPostIds = $daoSearch->getSearchPost($_GET['key']);
        if(!empty($searchPostIds)){
        $userIds = array();
        $imageIds = array();

        foreach($searchPostIds as $searchId){
            $userIds = $daoPostDb->getUserIdsByPostId($searchId);
            $imageIds = $daoPostDb->getPostImageByPostId($searchId);
            $postDate = $daoPostDb->getPostDateByPostId($searchId);
            $postImgs = $daoTshosaiDb->getPostImgByPostId($searchId);

            // ユーザーアイコンのSQL
            $pdo = new PDO('mysql:host=localhost; dbname=tabetterdb; charset=utf8',
            'webuser', 'abccsd2');

            $sql = "SELECT * FROM user_image WHERE user_id = ? ";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(1, $userIds, PDO::PARAM_STR);
            $stmt->execute();
            $image = $stmt->fetch(PDO::FETCH_ASSOC);

            $img = base64_encode($image['user_image']);
    ?>

    <!-- 投稿のカード -->
    <div class="card">
                <div class="card-body">
                    <div class="box">
                        <form action="userProfile.php" method="get">
                            <input type="image" src="data:<?=$image['image_type']?>;base64,<?=$img?>" class="profielIcon" />
                            <input type="hidden" name="id" value="<?=($userIds)?>">
                        </form>
                        <p class="userName"><?= $daoUserDb->getUserName($userIds)?></p>
                        <p class="userComment"><?= $daoPostDb->getPostDetail($searchId)?></p>
                        <?php

                        if(!empty($postImgs)){
                            echo '<div id="splider',($searchId),'" class="splide">';
                            echo '<div class="splide__track">';
                            echo '<ul class="splide__list">';
                        
                                foreach($postImgs as $Img){
                                $img = base64_encode($Img['post_image']);
                                echo '<li class="splide__slide">';
                                echo '<a href="T.syosai.php?post_id='.$searchId. '">';
                                echo '<img src="data:' .$Img['image_type'] .';base64,'.$img.'" width="100" class="postImage">';
                                echo '</a>';
                                echo '</li>';
                                }
                        
                            echo '</ul>';
                            echo '</div>';
                            echo '</div>';
                        }
                        ?>
                        <script>
                            new Splide( '#',($searchId),'' ).mount();
                        </script>
                    </div>
                    <div class="row row-eq-height">
                        <div class="col-6">
                            <div class="d-flex justify-content-end">
                                <?php
                                //自分が投稿にいいねしていたらtrueを返すフラグ
                                $flg = $daoPostDb->getLikeDetail($searchId,$_SESSION['user_id']);
                                //trueだったらオレンジのいいねボタンを適用
                                if($flg == 'true'){ ?>
                                    <div class="likeButton">
                                        <a href="T.syosai.php?post_id=<?= $postId ?>"><img src="../svg/Like-orange.png" class="likeButtonImg"/></a>
                                    </div>
                                    <div class="like" id="likeCnt">
                                        <?= $daoPostDb->getPostCount($searchId)?>
                                    </div>
                                    <?php } else { ?>
                                        <div class="likeButton">
                                    <a href="T.syosai.php?post_id=<?= $searchId ?>"><img src="../svg/Like-black.png" class="likeButtonImg"/></a>
                                    </div>
                                    <div class="like" id="likeCnt">
                                        <?= $daoPostDb->getPostCount($searchId)?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex justify-content-center">
                                <a href="T.syosai.php?post_id=<?= $searchId ?>"><img src="../svg/comment.svg" id="commentButton"></a>
                                <div class="comment">
                                    <?= $daoPostDb->getPostCommentCount($searchId)?>
                                </div>
                            </div>
                        </div>                                           
                    </div>
                    <div class="postDate">
                        <?= ($postDate) ?>
                    </div>
                </div>
            </div>
            
        <?php
        }
        ?>
    </div>
</div>

<?php
}else{
?>

<h6 class="text-center mt-5">「<?= $_GET['key']; ?>」の検索結果はありません</h6>


<?php

}

}else{

?>

  <div class="container-fluid">
    <div class="row">

    <?php
        $postIds = array();
        $postIds = $daoPostDb->getPostIds();
        $userIds = array();
        $imageIds = array();
        $postDate = array();

        foreach($postIds as $postId){
            $userIds = $daoPostDb->getUserIdsByPostId($postId);
            $imageIds = $daoPostDb->getPostImageByPostId($postId);
            $postDate = $daoPostDb->getPostDateByPostId($postId);
            $imageIds = array();
            $postImgs = $daoTshosaiDb->getPostImgByPostId($postId);

            // ユーザーアイコンのSQL
            $pdo = new PDO('mysql:host=localhost; dbname=tabetterdb; charset=utf8',
            'webuser', 'abccsd2');

            $sql = "SELECT * FROM user_image WHERE user_id = ? ";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(1, $userIds, PDO::PARAM_STR);
            $stmt->execute();
            $image = $stmt->fetch(PDO::FETCH_ASSOC);

            $img = base64_encode($image['user_image']);

    ?>
            <!-- 投稿のカード -->
            <div class="card">
                <div class="card-body">
                    <div class="box">
                        <form action="userProfile.php" method="get">
                            <input type="image" src="data:<?=$image['image_type']?>;base64,<?=$img?>" class="profielIcon" />
                            <input type="hidden" name="id" value="<?=($userIds)?>">
                        </form>
                        <p class="userName"><?= $daoUserDb->getUserName($userIds)?></p>
                        <p class="userComment"><?= $daoPostDb->getPostDetail($postId)?></p>
                        <?php

                        if(!empty($postImgs)){
                            echo '<div id="splider',($postId),'" class="splide">';
                            echo '<div class="splide__track">';
                            echo '<ul class="splide__list">';
                        
                                foreach($postImgs as $postImg){
                                $img = base64_encode($postImg['post_image']);
                                echo '<li class="splide__slide">';
                                echo '<a href="T.syosai.php?post_id='.$postId. '">';
                                echo '<img src="data:' .$postImg['image_type'] .';base64,'.$img.'" width="100" class="postImage">';
                                echo '</a>';
                                echo '</li>';
                                }
                        
                            echo '</ul>';
                            echo '</div>';
                            echo '</div>';
                        }
                        ?>
                        <script>
                            new Splide( '#',($postId),'' ).mount();
                        </script>
                    </div>
                    <div class="row row-eq-height">
                        <div class="col-6">
                            <div class="d-flex justify-content-end">
                                <?php
                                //自分が投稿にいいねしていたらtrueを返すフラグ
                                $flg = $daoPostDb->getLikeDetail($postId,$_SESSION['user_id']);
                                //trueだったらオレンジのいいねボタンを適用
                                if($flg == 'true'){ ?>
                                    <div class="likeButton">
                                        <a href="T.syosai.php?post_id=<?= $postId ?>"><img src="../svg/Like-orange.png" class="likeButtonImg"/></a>
                                    </div>
                                    <div class="like" id="likeCnt">
                                        <?= $daoPostDb->getPostCount($postId)?>
                                    </div>
                                    <?php } else { ?>
                                        <div class="likeButton">
                                    <a href="T.syosai.php?post_id=<?= $postId ?>"><img src="../svg/Like-black.png" class="likeButtonImg"/></a>
                                    </div>
                                    <div class="like" id="likeCnt">
                                        <?= $daoPostDb->getPostCount($postId)?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex justify-content-center">
                                <a href="T.syosai.php?post_id=<?= $postId ?>"><img src="../svg/comment.svg" id="commentButton"></a>
                                <div class="comment">
                                    <?= $daoPostDb->getPostCommentCount($postId)?>
                                </div>
                            </div>
                        </div>                                           
                    </div>
                    <div class="postDate">
                        <?= ($postDate) ?>
                    </div>
                </div>
            </div>
        <?php
        }
        ?>
    </div>
</div>

<?php 

}


?>

</div>



<div id="modal" class="modal">
    <div id="overlay" class="modal-content">
    <div id="content" class="content">
    <form method="POST" action="../DAO/post_imagesdb.php" enctype="multipart/form-data">
        <div class="row">
           <div class="col-2">
            <img src="data:<?php echo $image2['image_type'] ?>;base64,<?php echo $img2; ?> " class="profielIcon">
            </div>
            <div class="col-10 mr-5 pt-2">
            <?= $daoUserDb->getUserName($_SESSION['user_id']); ?>
            </div>
            </div>
            <div class="form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <textarea name="detail" class="form-control" id="exampleTextBox" rows="5"></textarea>
                        </div>
                    </div>
            </div>
            <div class="row">
                <div class="col-10">
                    <details>
                        <summary class="float-right mr-3">詳細</summary>
                            <input type="text" name="store" id="textboxstyle" placeholder="店名" class="text-center">
                            <input type="text" name="menu" id="textboxstyle" placeholder="メニュー名" class="text-center">
                            <input type="text" name="price" id="textboxstyle" placeholder="価格" class="text-center">
                            <input type="text" name="address" id="textboxstyle" placeholder="場所" class="text-center">
                    </details>
                </div>

                <div class="col-2">
                    <label class="float-right mr-3">
                        <span class="filelabel">
                            <img src="../svg/imagefile.svg" alt="" id="file-iamge">
                        </span>
                        <input type="file" name="image[]" multiple id="file-send" class="filesend">
                        <input type="hidden" name="userid" value="<?= $_SESSION['user_id']?>">
                    </label>
                </div>
            </div>

            <div class="row">
                <div class="col-10"></div>
                <div class="col-2 mt-2">
                <input type="submit" value="送信" class="buttonsubmit">
                </div>
            </div>
            
            
        </form>
    <button onclick="closeModal()">キャンセル</button>
    </div>
    </div>
</div>




 <!-- navigationBar -->
 <div class="navigation">
<div class="border"></div>
    <a class="list-link" href="timeLine.php">
        <i class="icon">
            <img src="../svg/time2.svg" class="image-size">
        </i>
    </a>
    <a class="list-link" href="forum.php">
        <i class="icon">
            <img src="../svg/forum.svg" class="image-size1">
        </i>
    </a>
    <a class="list-link" onclick="openModal()">
        <i class="icon">
            <img src="../svg/post.svg" class="image-size">
        </i>
    </a>
    <a class="list-link" href="myProfile.php">
        <i class="icon">
            <img src="../svg/profile.svg" class="image-size">
        </i>
    </a>
</div>
    <script src="../js/Oyamadaprofile.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <!-- <script src="../js/OyamadaBar.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js" integrity="sha256-FZsW7H2V5X9TGinSjjwYJ419Xka27I8XPDmWryGlWtw=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
    <script src="../js/time.js"></script>
</body>
</html>