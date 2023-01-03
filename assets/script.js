function updateHeadline(title, picture, content) {
    document.getElementById('headlineTitle').innerHTML = title;
    document.getElementById('headlinePicture').setAttribute('src', '/uploads/' + picture);
    document.getElementById('headlineContent').innerHTML = content;
}

document.getElementById('changeHeadlineButton').addEventListener('click', function () {
    fetch("/api/articles/random")
        .then(response => response.json())
        .then(function(article){
            updateHeadline(article[0].title, article[0].picture, article[0].summary)
        })
});

document.getElementById('searchHeadline').addEventListener('input', function(e) {
    //Here we get the value typed in the input
    //add loader
    let search = e.target.value;
    fetch("/api/articles/search?q="+search)
        .then(response => response.json())
        .then(function(articles){
            //remove loader
            const resultList = document.getElementById("resultList");
            resultList.innerHTML = "";
            for(article of articles) {
                const li = document.createElement("li");
                li.innerHTML = `<a href="/articles/${article.id}">${article.title}</a>`;
                resultList.append(li);
            }

        })
});
