<?php
// Включаем строгую типизацию
declare(strict_types=1);

/**
 * @param $some
 * отладочная функция
 */
function dd($some){
    echo '<pre>';
    print_r($some);
    echo '</pre>';
    exit();
}

/**
 * @param $url
 * редирект на указаный URL
 */
function goUrl(string $url){
    echo '<script type="text/javascript">location="';
    echo $url;
    echo '";</script>';
}

/**
 * функция возвращает масив статей
 * @return array
 */
function getArticles() : array
{
    return json_decode(file_get_contents('db/articles.json'), true);
}

/**
 * функция возвращает статью  в виде масива по id
 * @param int $id
 * @return array
 */
function getArticleById(int $id):array
{
    $articleList =getArticles();
    $curentArticle = [];
    if (array_key_exists($id, $articleList)) {
        $curentArticle = $articleList[$id];
    }
    //dd($curentArticle);
    return $curentArticle;
}

/**
 * функция генерирует список <li> из Json
 * и формирует ссылки вида URI index.php?id=1
 *
 * @return string
 */
function getArticleList(): string
{
    $articles = getArticles();
    $link = '';
    foreach ($articles as $article) {
        $link .= '<li class="nav-item"><a class="nav-link" href="index.php?id='. $article['id']
            . '">'. $article['title']. '</a></li>';
    }
    return $link;
}

function renderAddArticleForm(){
    return '<form action="/inc/admin.php" method="get">
  <div class="mb-3">
    <label class="form-label">Название</label>
    <input type="text" name="title" class="form-control">
  </div>
  <div class="mb-3">
    <label class="form-label">Картинка</label>
    <input type="text" name="image" class="form-control" >
  </div>
  <div class="form-floating">
  <textarea class="form-control" placeholder="Leave a comment here" id="floatingTextarea" name="content"></textarea>
  <label for="floatingTextarea">Описание</label>
</div>
<input type="hidden" name="act" value="store">
  <button type="submit" class="btn btn-primary">Сохранить</button>
</form>';
}

function renderEditArticleForm(array $article){
    return '<form action="./admin.php" method="get">
  <div class="mb-3">
    <label class="form-label">Название</label>
    <input type="text" name="title" class="form-control" value="'.$article['title'].'">
  </div>
  <div class="mb-3">
    <label class="form-label">Картинка</label>
    <input type="text" name="image" class="form-control" value="'.$article['image'].'">
  </div>
  <div class="form-floating">
  <textarea class="form-control" placeholder="Leave a comment here" id="floatingTextarea" name="content">
  '.$article['content'].'
</textarea>
  <label for="floatingTextarea">Описание</label>
</div>
<input type="hidden" name="act" value="update">
<input type="hidden" name="id" value="'.$article['id'].'">
  <button type="submit" class="btn btn-primary">Сохранить</button>
</form>';
}

function renderArticleList(): string
{
    $articles = getArticles();
    $link = '<a href="admin.php?act=add" class="btn btn-primary">Добавить</a>';
    foreach ($articles as $article) {
        $link .= '<a class="nav-link" href="admin.php?act=edit&id='. $article['id']
            . '">'. $article['title']. '</a>';
    }
    return $link;
}

function articleStore()
{
    $article =[];
    $articles = getArticles();
    $new_id = count($articles)+1;
    if(!empty($_REQUEST['title']) && !empty($_REQUEST['image'])&& !empty($_REQUEST['content'])  ){
        $article['id'] = $new_id;
        $article['title'] = $_REQUEST['title'];
        $article['image'] = $_REQUEST['image'];
        $article['content'] = $_REQUEST['content'];
        $articles[$new_id] = $article;
        storeArticlesInToFile($articles);
        goUrl('admin.php');
    }
}
function adminMain()
{
    if(isset($_REQUEST['act'])){
        switch ($_REQUEST['act']){
            case "store":
                articleStore();
                break;
            case "add":
                echo renderAddArticleForm();
                break;
            case "update":
                articleUpdate();
                break;
            case "edit":
                if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
                    $article = getArticleById( (int) $_REQUEST['id']);
                    echo renderEditArticleForm($article);
                }
                break;
        }
    }
    else{
        echo renderArticleList();
    }
}

function storeArticlesInToFile(array $articles)
{
    $json = json_encode($articles);
    file_put_contents('db/articles.json', $json);
}
function renderArticleCard(array $article, $single = false) : string
{
    //$content ='';
    $content ='
            <div class="card">
    <img src="'.$article['image'].'" height="200" width="494"/>
    <div class="card-body">
        <h5 class="card-title">'.$article['title'].'</h5>
        ';

    if($single == true)
    {
        $content .= '<p class="card-text"> '.$article['content'].'</p>';
    }
    else{
        $content .= '<div class="btn-group">
                            <a href="index.php?id='. $article['id'].'" class="btn btn-primary">Подробнее</a> 
                        </div>';
    }

    $content .= '</div>
</div>';
    return $content;
}
function renderArticlesCardList()
{
    $articles = getArticles();
    $article_list = '';
    foreach ($articles as $article) {
        $article_list .= renderArticleCard($article);
    }
    return $article_list;
}

function main():string
{
    if(isset($_GET['id']))
    {
        $id = (int)$_GET['id'];
        $article = getArticleById($id);
    }
    else{
        $article = '';
    }

    if(empty($article))
    {
        $content = renderArticlesCardList();
    }
    else{
        $content = renderArticleCard($article, true);
    }
    return $content;
}

function calculator($number1, $number2, $operation)
{
    $message='';
    if(isset($_POST['submit'])) {

        $number1 = $_POST['number1'];
        $number2 = $_POST['number2'];
        $operation = $_POST['operation'];
    }
    if(!$operation || (!$number1 && $number1 != '0') || (!$number2 && $number2 != '0')) {
        $error_result = 'Не все поля заполнены';
    }

    else {
        if(!is_numeric($number1) || !is_numeric($number2)) {
            $error_result = "Операнды должны быть числами";
        }
        else
            switch($operation){
                case 'plus':
                    $result = $number1 + $number2;
                    $message=$number1.'+'.$number2.'='.$result;
                    break;
                case 'minus':
                    $result = $number1 - $number2;
                    $message=$number1.'-'.$number2.'='.$result;
                    break;
                case 'multiply':
                    $result = $number1 * $number2;
                    $message=$number1.'*'.$number2.'='.$result;
                    break;
                case 'divide':
                    if( $number2 == '0')
                        $error_result = "На ноль делить нельзя!";
                    else
                        $result = $number1 / $number2;
                    $message=$number1.'/'.$number2.'='.$result;
                    break;
            }
    }
    if(isset($error_result)) {
        echo "Ошибка: $error_result";
    }
    else {
        echo $message;
    }
}