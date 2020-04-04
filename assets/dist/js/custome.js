function countWords(s){
    // s = s.toString();
    // s = s.replace(/(^\s*)|(\s*$)/gi,"");//exclude  start and end white-space
    // s = s.replace(/[ ]{2,}/gi," ");//2 or more space to 1
    // s = s.replace(/\n/g, " "); // exclude newline with a start spacing
    // // s = s.replace(/\n /,"\n"); // exclude newline with a start spacing
    // // return s.split(/(\s)/).filter(function(str){return str!="";}).length;
    // return s.split(/(\s)/).filter(function(str){return /\S/.test(str);}).length;

    return s.length;

    //return s.split(' ').filter(String).length; - this can also be used
}

$(function () {
	 $('.textarea').summernote()
  function registerSummernote(element, placeholder, max, callbackMax) {
    $(element).summernote({
      placeholder,
      callbacks: {
        onKeydown: function(e) {
        // console.log("fgfdg");
          var t = e.currentTarget.innerText;
          
           
          if (countWords(t) >= max) {
            //delete key
            if (e.keyCode != 8){
              if (e.keyCode == 32) {
                e.preventDefault();
              }
            }
            // add other keys ...
          }
        },
        onKeyup: function(e) {
            var t = e.currentTarget.innerText;
            if (typeof callbackMax == 'function') {
              callbackMax(countWords(t));
            }
        },
        onPaste: function(e) {
          // console.log("fgf");
          var t = e.currentTarget.innerText;
          var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
          e.preventDefault();
          var all = t + bufferText;
          document.execCommand('insertText', false, all.trim().substring(0, 3000));
          if (typeof callbackMax == 'function') {
            callbackMax(countWords(t));
          }
        }
      }
    });
  }
$(function(){
  registerSummernote('.textarea1.custom', 'Content Description', 3000, function(max) {
    $('#maxContentPost').text("Letter Count: " + max);
  });
});

});
	