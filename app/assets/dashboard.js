import DataTable from 'datatables.net-bs5'
import 'bootstrap'
import './styles/dashboard.css';
import datatable_en from 'datatables.net-plugins/i18n/en-GB.json'
import datatable_it from 'datatables.net-plugins/i18n/it-IT.json'


console.log('Cobalto Admin Dashboard')


function getLanguageFile(locale) {
    const languages = {
        en: datatable_en,
        it: datatable_it,
        // Add more mappings as needed
    };

    return languages[locale] || languages.en;
}


document.addEventListener('DOMContentLoaded', () => {

    const initDataTable = (selector) => {
        const table = document.querySelector(selector);
        if (table) {
            const tableLang = table.getAttribute('lang');
            const languageFile = getLanguageFile(tableLang);
            new DataTable(table, {
                language: languageFile
            });
        }
    };

    initDataTable('#users-table');
    initDataTable('#artists-table');
});



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



// Persist active link to local storage
document.addEventListener("DOMContentLoaded", function() {
    const sidebarNav = document.getElementById('sidebar');
    const links = sidebarNav.getElementsByClassName('nav-link');


    function setActiveLink(link) {
        // Remove the 'active' class from all links
        for (let j = 0; j < links.length; j++) {
            links[j].classList.remove('active');
        }
        // Add the 'active' class to the clicked link
        link.classList.add('active');
        // Save the active link href to localStorage
        localStorage.setItem('activeLink', link.getAttribute('href'));
    }

    for (let i = 0; i < links.length; i++) {
        links[i].addEventListener('click', function() {
            setActiveLink(this);
        });
    }

    const activeLinkHref = localStorage.getItem('activeLink');
    if (activeLinkHref) {
        for (let i = 0; i < links.length; i++) {
            if (links[i].getAttribute('href') === activeLinkHref) {
                links[i].classList.add('active');
                break;
            }
        }
    }
});




