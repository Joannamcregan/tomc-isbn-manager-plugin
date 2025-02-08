import $ from 'jquery';

class ISBNRegistrations{
    constructor(){
        this.addInfoButtons = $('.add-isbn-info-button');
        this.isbnInfoOverlay = $('#tomc-isbn-edit-info-overlay');
        this.overlayCloseButton = $('#isbn-info-overlay__close');
        this.audioSection = $('#isbn-info--audio-section');
        this.ebookSection = $('#isbn-info--ebook-section');
        this.printSection = $('#isbn-info--print-section');
        this.mediumSelect = $('#isbn-info--book-medium');
        this.events();
    }
    events(){
        this.addInfoButtons.on('click', this.showInfo.bind(this));
        this.overlayCloseButton.on('click', this.closeOverlay.bind(this));
        this.mediumSelect.on('change', (e)=>{
            $('.isbn-info--format-section').addClass('hidden');
            $('#isbn-info--section-' + $(e.target).val()).removeClass('hidden');
        })
    }
    showInfo(e){
        console.log($(e.target).closest('.tomc-isbn-field-section').data('isbn'));
        this.isbnInfoOverlay.addClass('search-overlay--active');
        this.isbnInfoOverlay.find('h2').append($(e.target).closest('.tomc-isbn-field-section').data('isbn'));
    }
    closeOverlay(e){
        this.isbnInfoOverlay.find('h2').html('Add Info for ISBN ');
        this.isbnInfoOverlay.removeClass('search-overlay--active');
    }
}

export default ISBNRegistrations;