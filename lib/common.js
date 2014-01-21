var common =  {
	generateRandomString: function(size)
	{
		var chars = new Array("R","T","a","0","1","F","b","8","I","G","c","d","e","H","f","9","g","h","i","j","k","l","m","X","Y","K","n","2","-","o","Z","J","p","A","B","q","7","Q","r","S","s","t","L","M","u","E","v","w","x","3","y","U","_","V","W","4","z","C","D","N","O","P","5","6");
		var str ='';
		
		for(i = 0; i < size; i++)
		{
			str = str + chars[Math.floor(Math.random()*chars.length)];
		}
		return str;
	}
}

module.exports = common;