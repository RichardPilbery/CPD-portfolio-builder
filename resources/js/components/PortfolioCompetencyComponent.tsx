import React, { useState, useEffect } from 'react';
import ReactDOM from 'react-dom';

type PortfolioCompetencyComponentProps = {
    comps?: string,
    selcomps?: string
}

type compListProps = {
    id: number,
    value: string,
    name: string
}

const PortfolioCompetencyComponent = ({comps, selcomps}:PortfolioCompetencyComponentProps) => {

    const [competencies] = useState(JSON.parse(comps));
    const [selcompetencies] = typeof selcomps !== "undefined" ? useState(JSON.parse(selcomps)) : {};
    const [processedComps, setprocessedComps] = useState([]);
    const [query, setQuery] = useState("");
    const [displayComps, setDisplayComps] = useState([]);

    function delComp(value: any) {
        var tempArray = displayComps;
        // https://stackoverflow.com/a/40537851/3650230
        var index = displayComps.findIndex(function(e) {
            return e.id == value;
        });
        if (index !== -1) tempArray.splice(index, 1);
        // https://stackoverflow.com/a/56266640/3650230
        setDisplayComps([...tempArray]);
    }

    function addComp(result) {
        var tempArray = displayComps.concat(result);
        setDisplayComps(tempArray);
        var inputField = document.getElementById("compsearch") as HTMLInputElement;
        inputField.value == "";
    }

    function addAll() {
        setDisplayComps([...processedComps]);
    }

    function processComps(compdata) {
        var tempArray = [];
        for (const [key, value] of Object.entries(compdata)) {
            tempArray.push({
                id: key,
                value: value,
                name: 'comp[' + key + ']'
             });
        }
        return tempArray;
    }

    useEffect(() => {
        console.log('query is ' + query);
    }, [query])

    useEffect(() => {
        // Prepare the JSON array so it can be searched and provide
        // identifiers when returned to server
        setprocessedComps(processComps(competencies));
        if(typeof selcompetencies != 'undefined' && processComps(selcompetencies).length > 0) {
            console.log("Selected competencies");
            setDisplayComps(processComps(selcompetencies));
        }
    }, []);

    return(
        <div className="mb-4 pt-3">
            <label className="block uppercase text-gray-700 text-sm font-bold mb-2" htmlFor="compsearch">Competencies (e.g. KSF)</label>
            <div className="flex">
                <input id="compsearch" name="compsearch" className="shadow appearance-none border rounded w-10/12 py-2 px-3 leading-tight mr-6" type="search"  placeholder="Enter a search term here" onChange={event => setQuery(event.target.value)} onBlur={event => {event.target.value = ""}} />
                <button className="shadow appearance-none bg-green-600 hover:bg-green-700 border-green-600 hover:border-red-700 leading-tight border text-white py-2 px-3 rounded w-2/12" type="button" onClick={() => addAll()}>
                    Add All
                </button>
            </div>

            <PortfolioCompetencyOptionsComponent query={query} comps={processedComps} addCompClick={addComp} displayC={displayComps} />

            <PortfolioSelectedCompentenciesComponent displayC={displayComps} delComp={delComp}/>

        </div>
    );
}
export default PortfolioCompetencyComponent;

const PortfolioSelectedCompentenciesComponent = ({displayC, delComp}: any) => {

    var tempArray = [];
    typeof displayC !== "undefined" && displayC.length > 0 &&
        displayC.sort(function(a, b) {
            var textA = a.value.toUpperCase();
            var textB = b.value.toUpperCase();
            return (textA < textB) ? -1 : (textA > textB) ? 1 : 0;
        });
        displayC.forEach((result) => {
            tempArray.push(
                <div className="pt-2 flex" key={result.id}>
                    <input id={result.id} name={result.name} className="shadow appearance-none border rounded w-10/12 py-2 px-3 text-gray-700 leading-tight bg-gray-200 mr-6" type="text" value={result.value} readOnly />
                    <button className="shadow appearance-none bg-red-600 hover:bg-red-700 border-red-600 hover:border-red-700 leading-tight border text-white py-2 px-3 rounded w-2/12" type="button" onClick={event => delComp(event.target.parentNode.getElementsByTagName("input")[0].id)}>
                        Remove
                    </button>
                </div>
            );
    });

    return(tempArray);

}

type PortfolioCompetencyOptionsComponentProps = {
    query: string,
    comps: [],
    addCompClick: any,
    displayC: any,
}

type compsObjectProps = {
    id: string,
    value: string,
    name: string,
}

const PortfolioCompetencyOptionsComponent = ({query, comps, addCompClick, displayC}: PortfolioCompetencyOptionsComponentProps) => {
    const [selectedComps, setSelectedComps] = useState([]);

    function clickedComp(result) {
        setSelectedComps([]);
        addCompClick(result);
    }

    useEffect(() => {
        if(query.length > 2) {
            var tempResults = [];
            const tempArray = [];
            // Need to remove competencies that have already been selected
            const arrayNoAlreadySelected = [].concat(
                comps.filter((obj1: compsObjectProps) => displayC.every((obj2: compsObjectProps)  => obj1.id !== obj2.id)),
                displayC.filter((obj2: compsObjectProps) => comps.every((obj1: compsObjectProps) => obj2.id !== obj1.id))
            );
            console.log(arrayNoAlreadySelected);
            tempResults = arrayNoAlreadySelected.filter((item: compListProps) => {
                return item.value.toLowerCase().indexOf(query.toLowerCase()) > -1;
            });
            tempResults.forEach((result) => tempArray.push(
                <li key={result.id} name={result.name} className="py-1 px-1 big-text" onClick={() => clickedComp(result)}>
                    {result.value}
                </li>
            ));
            setSelectedComps(tempArray);
        } else {
            setSelectedComps(undefined);
        }
    }, [query]);

    return(
        <>
        {
            typeof selectedComps !== 'undefined' && selectedComps.length > 0 ?
            <div>
                <ul className="bg-blue-100 px-2 py-2 shadow-lg border-2 rounded mt-2 absolute z-50 mr-2 border-blue-700">
                    {selectedComps}
                </ul>
            </div>
            : null
        }
        </>
    )
}

if (document.getElementById('portfolio_competency_component')) {
    const element = document.getElementById('portfolio_competency_component');
    // create new props object with element's data-attributes
    const props = Object.assign({}, element.dataset);
    ReactDOM.render(<PortfolioCompetencyComponent {...props}/>,
        document.getElementById('portfolio_competency_component'));
}
