document.querySelector(".collapsible").addEventListener("click", function()
{
    var content = this.nextElementSibling;
    if (content.style.maxHeight)
    {
      content.style.maxHeight = null;
    }
    else
    {
      content.style.maxHeight = content.scrollHeight + "px";
    }
});