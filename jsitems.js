function displayaction(event){
    event.preventDefault()
        console.log(event.target.nextElementSibling)
        if (event.target.nextElementSibling.style.display === 'block') {
            event.target.nextElementSibling.style.display = 'none'
                } else {
                    event.target.nextElementSibling.style.display = 'block'
        }
}


function updateemployees(event){
        console.log(event.target.parentNode.nextElementSibling.parentNode.nextElementSibling)
        if (event.target.parentNode.nextElementSibling.parentNode.nextElementSibling.style.display === 'table-row') {
            event.target.parentNode.nextElementSibling.parentNode.nextElementSibling.style.display = 'none'
                } else {
                    event.target.parentNode.nextElementSibling.parentNode.nextElementSibling.style.display = 'table-row'
        }
}

