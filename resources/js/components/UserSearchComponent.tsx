import axios from 'axios';
import React, { useState, useEffect } from 'react';
import ReactDOM from 'react-dom';


const UserSearchComponent = () => {

    const [results, setResults] = useState();
    const [query, setQuery] = useState("");

    function searchUsers(query) {
        if(query.length > 2) {
            axios.get('/user/search/'+query)
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
        searchUsers(query);
    }, [query]);

    useEffect(() => {
        // console.log(results);
    }, [results]);

    return(
            <div>
                <div>
                    <input type="search" placeholder="Search for user" id="query" name="query" className="w-full text-gray-700 border border-gray-500 rounded-full py-2 px-4 leading-tight focus:outline-none focus:bg-blue-100 focus:border-blue-500" onChange={event => setQuery(event.target.value)}/>
                </div>
                {
                typeof results !== 'undefined' && results.length > 0 &&
                <div>
                    <ul className="bg-blue-100 px-2 py-2 shadow-lg border-2 rounded mt-2 absolute z-50 mr-2 border-blue-700">
                        {
                            results.map((result, i) => {
                                return(
                                    <li key={i} className="py-1 px-1 big-text">
                                        <a href={"/user/"+result.id+"/0/edit"} >
                                            {result.id} {result.name}: {result.email}
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

export default UserSearchComponent;

if (document.getElementById('user_search_component')) {
    ReactDOM.render(<UserSearchComponent />, document.getElementById('user_search_component'));
}
