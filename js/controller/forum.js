/**
 * Control Draft editor actions
 *
 * @package draft
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */

var forumControllerImpl = function() {
    "use strict";

    /**
     * Returns a beautified dice string
     */
    var beautifyDice = function(original){
        var newString = original.replace(/d([0-9fu]*)(.?) \( (.*?) \)/g, function($value, $1, $2, $3){
            //See if the dice we are currently looking at has exploded
            var exploded = parseInt($1)<parseInt($3);

            //Return the replaced string
            return '<span class="dice dice_'+$1+' '+(exploded?'dice_exploded':'')+'">'+$3+'</span>';
        });

        //Replace the dice string and return the new version
        return newString != original ? '<span class="diceLine">'+newString+'</span>':original;
    };

    function onForumLoaded() {
        //Testing regexp
        var reg = /.*? a lancé .*? et a obtenu : (.*?)Description : .*/i;

        //For each post, see if it is a dice roll
        $('.postDice').each(function(index, post){
            //Look for dices
            var dice = reg.exec(post.innerHTML);

            if(dice === null){
                //If not a dice roll, exit and try next post
                return;
            }

            //Change using beautified dices
            post.innerHTML = post.innerHTML.replace(dice[1], beautifyDice(dice[1]));
        });

        //Foreach dicer result
        $('#resultatDicerTable td:last-child').each(function(index, line){
            line.innerHTML = beautifyDice(line.innerHTML);
        });
    }

    return {
        onForumLoaded : onForumLoaded,
        beautifyDice: beautifyDice
    };
};

var forumController = forumControllerImpl();
onLoadController.generals.push(forumController.onForumLoaded);
