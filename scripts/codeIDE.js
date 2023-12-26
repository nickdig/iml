function activateAce(){
    var editor = ace.edit(document.getElementById("editor"), {
        mode: "ace/mode/java",
        selectionStyle: "text"
    });
    editor.setTheme("ace/theme/textmate");
    editor.session.setTabSize(4);
    editor.setShowPrintMargin(false);
    editor.setFontSize("16px");
}
activateAce();