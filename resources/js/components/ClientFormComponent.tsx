import axios from 'axios';
import React, { useState, useEffect } from 'react';
import ReactDOM from 'react-dom';


const ClientFormComponent: React.FC = () => {

    const processForm = (event: any) => {
        console.log('Inside Process Form')
        event.preventDefault();
        const data = {
            "name": event.target.name.value,
            "redirect": event.target.redirect.value
        };
        console.log(data);

        axios.post('/oauth/clients', data)
        .then(response => {
            console.log(response);
            window.location.reload();

        })
        .catch (response => {
            // List errors on response...
            console.log(response);
        });
        
    }

    return (
        <>
        <div className="m-4 p-4 border-2 border-blue-700 rounded">
            <form onSubmit={processForm}>

                <div className="mb-4">
                    <label className="block uppercase text-gray-700 text-sm font-bold mb-2" htmlFor="name">
                        Client name
                    </label>
                    <input className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="name"  name="name" type="text" placeholder="Standby CPD"/>
                </div>

                <div className="mb-4">
                    <label className="block uppercase text-gray-700 text-sm font-bold mb-2" htmlFor="name">
                        Redirect URL
                    </label>
                    <input className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="redirect"  name="redirect" type="text" placeholder="https://my-url.com/callback" />
                </div>

                <br />

                <button type="submit" className="btn w-full">Create Client</button>

            </form>
        </div>
        </>
    )
};




if (document.getElementById('client_form_component')) {
    const element = document.getElementById('client_form_component');
    // create new props object with element's data-attributes
    const props = Object.assign({}, element.dataset);
    ReactDOM.render(<ClientFormComponent {...props}/>,
        document.getElementById('client_form_component'));
}
