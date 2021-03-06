<?php 

require_once "Post.php";

class PostMethods
{
  private $pdo;

  // PDO object gets passed as a parameter to encapsulate the database operations for Posts inside this class
  public function __construct($pdo)
  {
    $this->pdo = $pdo;
  }

  // Creates a post
  public function create(Post $new_post)
  {    
  	$statement = $this->pdo->prepare(
      "INSERT INTO posts (title, description, image, created_by, date, category) VALUES (:title, :description, :image, :created_by, :date, :category)"
    );
    
  	$statement->execute ([
  		":title" => $new_post->get_title(),
  		":description" => $new_post->get_description(),
      ":image" => $new_post->get_image(),
      ":created_by" => $new_post->get_created_by(),
      ":date" => $new_post->get_date(),
      ":category" => $new_post->get_category()
    ]);
  }

  // Lists all posts 
  public function list_all_posts()
  {
    try {
      $statement = $this->pdo->prepare(
        "SELECT * from posts ORDER BY date DESC"
      );

      $statement->execute();

      /* 
      * We will use FETCH_CLASS to return an array consisting of objects from the Post class
      * FETCH_CLASS fetches database rows into an object
      * With FETCH_CLASS, data is populated before the constructor is called 
      * Our constructor initializes the date, so to avoid that the values from the database are being overwritten with the same values every time, we simply add PDO::FETCH_PROPS_LATE when calling fetchAll so the constructor will be called first.
      */

      return $statement->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, "Post");

    } catch (PDOException $exception) {
      echo "Connection error" . $exception->getMessage();
    }
  }

  // Lists a single post
  public function list_single_post($id)
  {
    try {
      $statement = $this->pdo->prepare(
        "SELECT * from posts WHERE id = :id ORDER BY date DESC"
      );

      $statement->execute([
        ":id" => $id
      ]);

      // return a single post object. 
      $statement->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, "Post");

      return $statement->fetch();

    } catch (PDOException $exception) {
      echo "Connection error" . $exception->getMessage();
    }
  } 

  // Lists latest post
  public function list_latest_post()
  {
    try {
      $statement = $this->pdo->prepare(
        "SELECT * from posts ORDER BY date DESC LIMIT 1"
      );

      $statement->execute();

      // return a single post object 
      $statement->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, "Post");

      return $statement->fetch();

    } catch (PDOException $exception) {
      echo "Connection error" . $exception->getMessage();
    }
  }

  // Deletes a post
  public function delete_posts($id) 
  {
    $statement = $this->pdo->prepare(
      "DELETE from posts WHERE id = :id; DELETE from comments WHERE post_id = :id;"
    );

    $statement->execute([
      "id" => $id
    ]);
  }

  // Edits a post
  public function edit_post(Post $edited_post)
  {
    try{
      $statement = $this->pdo->prepare(
        "UPDATE posts SET title = :title, description = :description, image = :image, category = :category WHERE posts.id = :id;"
      );

      $statement->execute ([
        ":title" => $edited_post->get_title(),
        ":description" => $edited_post->get_description(),
        ":image" => $edited_post->get_image(),
        ":category" => $edited_post->get_category(),
        ":id" => $edited_post->get_id()
      ]);

    }catch (PDOException $exception) {
      echo "Connection error" . $exception->getMessage();
    }
  }
}

?>


