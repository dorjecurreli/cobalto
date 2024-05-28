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
    console.log( e.currentTarget.dataset.collectionHolderClass)

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
    deleteButton.addEventListener('click', function() {
        item.remove();
    });
}

