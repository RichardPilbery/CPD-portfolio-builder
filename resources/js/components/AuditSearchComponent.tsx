import axios from 'axios';
import React, { useState, useEffect } from 'react';
import ReactDOM from 'react-dom';
import moment from 'moment';


const AuditSearchComponent = () => {

    const [results, setResults] = useState();
    const [query, setQuery] = useState("");

    function searchAudits(query) {
        if(query.length > 2) {
            axios.get('/audit/search/'+query)
            .then(response => setResults(response.data))
            .catch(error => { console.log(error) });
        } else {
            setResults(undefined);
        }
    }

    function clearSearch(event) {
        // console.log(event);

    }
    // console.log("Inside portfolio search");

    useEffect(() => {
        // console.log("query value has changed");
        searchAudits(query);
    }, [query]);

    useEffect(() => {
        // console.log(results);
    }, [results]);

    return(
            <div>
                <div>
                    <input type="search" placeholder="Search audits entries" id="query" name="query" className="w-full text-gray-700 border border-gray-500 rounded-full py-2 px-4 leading-tight focus:outline-none focus:bg-purple-100 focus:border-purple-500" onChange={event => setQuery(event.target.value)}/>
                </div>
                {
                typeof results !== 'undefined' && results.length > 0 &&
                <div>
                    <ul className="bg-purple-100 px-2 py-2 shadow-lg border-2 rounded mt-2 absolute z-50 mr-2 border-purple-700">
                        {
                            results.map((result, i) => {
                                return(
                                    <li key={i} class="py-1 px-1 big-text">
                                        <a href={"/audit/"+result.id} >
                                            { moment(result.incdatetime, "YYYY-MM-DD HH:mm").locale('en-gb').format("LLL")} : {result.provdiag}
                                        </a>
                                    </li>
                                );
                            })
                        }
                    </ul>
                </div>
                }
            </div>
    );
}

export default AuditSearchComponent;

if (document.getElementById('audit_search_component')) {
    ReactDOM.render(<AuditSearchComponent />, document.getElementById('audit_search_component'));
}
