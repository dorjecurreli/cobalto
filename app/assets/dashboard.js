import DataTable from 'datatables.net-bs5'
import 'bootstrap'
import './styles/dashboard.css';


console.log('Cobalto Admin Dashboard')

new DataTable('#users-table');
new DataTable('#artists-table')

document
    .querySelectorAll('.add_item_link')
    .forEach(btn => {
        btn.addEventListener("click", addFormToCollection)
    });

function addFormToCollection(e) {
    const collectionHolder = document.querySelector('.' + e.currentTarget.dataset.collectionHolderClass);

    const item = document.createElement('div');
    item.classList.add('mb-3');

    item.innerHTML = collectionHolder
        .dataset
        .prototype
        .replace(
            /__artwork__/g,
            collectionHolder.dataset.index
        );

    collectionHolder.appendChild(item);

    collectionHolder.dataset.index++;

    addDeleteButtonListener(item);
}

function addDeleteButtonListener(item) {
    const deleteButton = item.querySelector('.delete-form-button');
    console.log(deleteButton);
    deleteButton.addEventListener('click', function() {
        console.log('pippo')
        item.remove();
    });
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-form-button').forEach(function(button) {
        button.addEventListener('click', function() {
            const cardContainer = this.closest('.card-container');
            if (cardContainer) {
                cardContainer.remove();
            }
        });
    });
});




// // Seleziona tutti gli elementi con la classe "vich-image"
// var vichImageDivs = document.querySelectorAll('.vich-image');
//
// // Ciclo attraverso ogni elemento .vich-image
// vichImageDivs.forEach(function(vichImageDiv) {
//     // Crea un nuovo div
//     var artworkImageDiv = document.createElement('div');
//     artworkImageDiv.className = 'artwork-image';
//
//     // Sposta l'anchor tag all'interno del nuovo div
//     var anchorTag = vichImageDiv.querySelector('a');
//     if (anchorTag) {
//         anchorTag.classList.add('open-modal')
//         anchorTag.removeAttribute('download');
//         anchorTag.setAttribute('href', '#');
//         anchorTag.setAttribute('data-bs-toggle', 'modal');
//         anchorTag.setAttribute('data-bs-target', '#imageModal');
//         artworkImageDiv.appendChild(anchorTag);
//     }
//
//     // Inserisci il nuovo div all'interno del div con la classe "vich-image"
//     vichImageDiv.appendChild(artworkImageDiv);
// });



