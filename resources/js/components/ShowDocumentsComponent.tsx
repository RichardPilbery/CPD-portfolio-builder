import React, { useState, useEffect } from 'react';
import ReactDOM from 'react-dom';

type ShowDocumentsComponentProps = {
    docs?: string,
}

const ShowDocumentsComponent: React.FC<ShowDocumentsComponentProps> = ({docs}) => {

    const [doc] = useState(JSON.parse(docs));
    const [displayDocs, setDisplayDocs] = useState([]);

    useEffect(() => {
        const tempDocs = [];
        doc.forEach((d) => {
            tempDocs.push(
                <div key={d.id} className="pt-2 flex">
                    <input id={d.id} name={`documents[${d.id}]`} className="shadow appearance-none border rounded w-10/12 py-2 px-3 leading-tight mr-6 bg-gray-200" type="text" value={d.origfilename !== null ? d.origfilename : ''} readOnly />
                    <button className="shadow appearance-none bg-red-600 hover:bg-red-700 border-red-600 hover:border-red-700 leading-tight border text-white py-2 px-3 rounded w-2/12" type="button" onClick={event => event.target.parentNode.remove()}>
                        Remove
                    </button>
                </div>
            )
        });
        setDisplayDocs(tempDocs);
    }, [doc]);

    useEffect(() => {
        console.log('Display Docs');
        console.log(displayDocs);
    }, [displayDocs]);

    return(
        <div className="mb-4 pt-3">
            {displayDocs}
        </div>
    )
}

export default ShowDocumentsComponent;

if (document.getElementById('show_documents_component')) {
    const element = document.getElementById('show_documents_component');
    // create new props object with element's data-attributes
    const props = Object.assign({}, element.dataset);
    ReactDOM.render(<ShowDocumentsComponent {...props}/>,
        document.getElementById('show_documents_component'));
}