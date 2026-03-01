require('./bootstrap');
require('alpinejs');
require('react');
require('react-dom');
require('moment');
import {
    Chart as ChartJS,
    registerables
  } from 'chart.js';

ChartJS.register(
    ...registerables
);

require('../js/components/ShowDocumentsComponent');
require('../js/components/AuditSearchComponent');
require('../js/components/AuditSkillComponent');
require('../js/components/AirwayComponent');
require('../js/components/VascularComponent');
require('../js/components/PortfolioCompetencyComponent');
require('../js/components/PortfolioSearchComponent');
require('../js/components/PortfolioCPDProfileDownloadComponent');
require('../js/components/UserSearchComponent');
require('../js/components/WordCountComponent');
require('../js/components/FlashComponent');

require('../js/components/PortfolioDownloadComponent');
require('../js/components/AuditDownloadComponent');
require('../js/components/AuditLogComponent');

require('../js/components/PortfolioTypesBarComponent');
require('../js/components/AuditLineComponent');

require('../js/components/ClientFormComponent');
require('../js/components/ClientDeleteComponent');


new ClipboardJS('.copy');

function updateWordCount() {
    console.log('updateWordCount called');
    var id = document.getElementsByClassName("word-count");
    if (id.length > 0) {
        //console.log(id);
        //console.log("Word counts are " + id.length);
        var word_total_element = document.getElementById("word-total");
        var word_total = 0;
        Array.prototype.map.call(id, function(x) {
            //console.log("Number is " + x.value);
            word_total += parseInt(x.value, 10);
        })
        word_total_element.textContent = word_total;
        console.log("Word total is " + word_total);
    }
}

