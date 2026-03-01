import axios from 'axios';
import React, { useState, useEffect } from 'react';
import ReactDOM from 'react-dom';

type ClientDeleteComponentProps = {
    clientId: number,
}


const ClientDeleteComponent: React.FC<ClientDeleteComponentProps> = ({clientid}) => {
    // console.log(clientid);

    const processDeletion = (event: any) => {

        console.log('Going to delete /oauth/clients/' + clientid);
        axios.delete('/oauth/clients/' + clientid)
        .then(response => {
            // Note that this marks them as revoked...it does not actually remove them from the database
            // console.log(response);
            window.location.reload();

        })
        .catch (response => {
            // List errors on response...
            // console.log('Error');
            // console.log(response);
        });
        
    }

    return (<button type="submit" className="text-red-600 hover:text-red-800" onClick={processDeletion}>Delete</button>)
};


if (document.getElementsByClassName('client_delete_component')) {
    const elements = Array.from(document.getElementsByClassName('client_delete_component') as HTMLCollectionOf<HTMLElement>);
    // create new props object with element's data-attributes
    elements.forEach((element) => {
        //console.log(element);
        const props = Object.assign({}, element.dataset);
        ReactDOM.render(<ClientDeleteComponent {...props}/>,element);
    });
}
