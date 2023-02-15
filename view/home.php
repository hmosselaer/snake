<!DOCTYPE html>
<html>
  <head>
  	<title>Snake</title>
  </head>
  <body>

    <div class="score">Score <span id="score">0</span></div>
<?php if($_SESSION['user']) { ?>
    <div class="user">Your score will be logged as <?=$_SESSION['user']?> <a href="javascript:change_user();">[Change User]</a></div>
<?php } else { ?>
    <div class="user">Your score is not logged <a href="javascript:change_user();">[Change User]</a></div>
<?php } ?>
    <div class="snakeboard"><canvas id="snakeboard" width="600" height="600"></canvas></div>
    <div id="gameover">Game Over!</div>
    <div id="scoreboard"></div>
 
    <style>
      .score {
        text-align: center;
        font-size: 60px;
      }
      .user {
        text-align: center;
        padding-top:5px;
        padding-bottom:20px;
      }
      .snakeboard {
        width:100%;
        text-align: center;
      }
      #gameover {
        display: none;
        text-align: center;
        font-size: 80px;
        line-height: 80px;
        color: red;
        margin-bottom:-10px;
      }
      #scoreboard {
        padding-top:15px;
        text-align: center;
      }
    </style>
  </body>

  <script>
    const board_border = 'black';
    const board_background = "white";
    const snake_col = 'lightblue';
    const snake_border = 'darkblue';
    
    let snake = [
      {x: 200, y: 200},
      {x: 190, y: 200},
      {x: 180, y: 200},
      {x: 170, y: 200},
      {x: 160, y: 200}
    ]

    let score = 0;
    let changing_direction = false;
    let food_x;
    let food_y;
    let dx = 10;
    let dy = 0;
    
    const snakeboard = document.getElementById("snakeboard");
    const snakeboard_ctx = snakeboard.getContext("2d");
    main();

    gen_food();

    document.addEventListener("keydown", change_direction);
    
    function main() {
	if (has_game_ended()) {
            document.getElementById('gameover').style.display = 'block';

<?php if($_SESSION['user']) { ?>
            doAPIcall(
                "POST",
                "/score/post?user=<?=$_SESSION['user']?>&score="+score,
                function (data) {
                    doAPIcall(
                       "GET",
                       "/score",
                       function (data2) {
                          document.getElementById("scoreboard").innerHTML = data2;
                       }
                )}
            );
<?php } ?>

	    return;
	}

        changing_direction = false;
        setTimeout(function onTick() {
	        clear_board();
	        drawFood();
	        move_snake();
	        drawSnake();
        	main();
        }, 100)
    }

    doAPIcall(
      "GET",
      "/score",
      function (data) {
        document.getElementById("scoreboard").innerHTML = data;
      }
    );
    
    function clear_board() {
      snakeboard_ctx.fillStyle = board_background;
      snakeboard_ctx.strokestyle = board_border;
      snakeboard_ctx.fillRect(0, 0, snakeboard.width, snakeboard.height);
      snakeboard_ctx.strokeRect(0, 0, snakeboard.width, snakeboard.height);
    }
    
    function drawSnake() {
      snake.forEach(drawSnakePart)
    }

    function drawFood() {
      snakeboard_ctx.fillStyle = 'lightgreen';
      snakeboard_ctx.strokestyle = 'darkgreen';
      snakeboard_ctx.fillRect(food_x, food_y, 10, 10);
      snakeboard_ctx.strokeRect(food_x, food_y, 10, 10);
    }
    
    function drawSnakePart(snakePart) {
      snakeboard_ctx.fillStyle = snake_col;
      snakeboard_ctx.strokestyle = snake_border;
      snakeboard_ctx.fillRect(snakePart.x, snakePart.y, 10, 10);
      snakeboard_ctx.strokeRect(snakePart.x, snakePart.y, 10, 10);
    }

    function has_game_ended() {
      for (let i = 4; i < snake.length; i++) {
        if (snake[i].x === snake[0].x && snake[i].y === snake[0].y) return true
      }
      const hitLeftWall = snake[0].x < 0;
      const hitRightWall = snake[0].x > snakeboard.width - 10;
      const hitToptWall = snake[0].y < 0;
      const hitBottomWall = snake[0].y > snakeboard.height - 10;
      return hitLeftWall || hitRightWall || hitToptWall || hitBottomWall
    }

    function random_food(min, max) {
      return Math.round((Math.random() * (max-min) + min) / 10) * 10;
    }

    function gen_food() {
      food_x = random_food(0, snakeboard.width - 10);
      food_y = random_food(0, snakeboard.height - 10);
      snake.forEach(function has_snake_eaten_food(part) {
        const has_eaten = part.x == food_x && part.y == food_y;
        if (has_eaten) gen_food();
      });
    }

    function change_direction(event) {
      const LEFT_KEY = 37;
      const RIGHT_KEY = 39;
      const UP_KEY = 38;
      const DOWN_KEY = 40;
      
      if (changing_direction) return;
      changing_direction = true;
      const keyPressed = event.keyCode;
      const goingUp = dy === -10;
      const goingDown = dy === 10;
      const goingRight = dx === 10;
      const goingLeft = dx === -10;
      if (keyPressed === LEFT_KEY && !goingRight) {
        dx = -10;
        dy = 0;
      }
      if (keyPressed === UP_KEY && !goingDown) {
        dx = 0;
        dy = -10;
      }
      if (keyPressed === RIGHT_KEY && !goingLeft) {
        dx = 10;
        dy = 0;
      }
      if (keyPressed === DOWN_KEY && !goingUp) {
        dx = 0;
        dy = 10;
      }
    }

    function move_snake() {
      const head = {x: snake[0].x + dx, y: snake[0].y + dy};
      snake.unshift(head);
      const has_eaten_food = snake[0].x === food_x && snake[0].y === food_y;
      if (has_eaten_food) {
        score += 10;
        document.getElementById('score').innerHTML = score;
        gen_food();
      } else {
        snake.pop();
      }
    }    

    function doAPIcall(type, url, callback) {
      var xmlhttp = new XMLHttpRequest();
      xmlhttp.onreadystatechange = function () {
      if (xmlhttp.readyState == XMLHttpRequest.DONE && xmlhttp.status == 200) {
          var data = xmlhttp.responseText;
          if (callback) callback(data);
          }
      };

      xmlhttp.open(type, url, true);
      xmlhttp.send();
    }

    function change_user() {
      let user = prompt("Please enter your name", "<?=$_SESSION['user']?>");

      window.location='./home?user='+user;

      return false;
    }
  </script>
</html>
