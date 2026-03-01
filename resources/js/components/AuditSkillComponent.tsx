import React, { useState, useEffect } from 'react';
import ReactDOM from 'react-dom';

type AuditSkillComponentProps = {
    skills?: string,
    selskills?: string
}

type SkillListProps = {
    id: number,
    value: string,
    name: string
}

const AuditSkillComponent: React.FC<AuditSkillComponentProps> = ({skills, selskills}) => {
    console.log(selskills);

    const [skillz] = useState(JSON.parse(skills));
    const [selskillz] = selskills.length > 0? useState(JSON.parse(selskills)) : {};
    const [processedSkills, setProcessedSkills] = useState([]);
    const [query, setQuery] = useState("");
    const [displaySkills, setDisplaySkills] = useState([]);

    function delSkill(value: any) {
        var tempArray = displaySkills;
        // https://stackoverflow.com/a/40537851/3650230
        var index = displaySkills.findIndex(function(e) {
            return e.id == value;
        });
        if (index !== -1) tempArray.splice(index, 1);
        // https://stackoverflow.com/a/56266640/3650230
        setDisplaySkills([...tempArray]);
    }

    function addSkill(result) {
        var tempArray = displaySkills.concat(result);
        setDisplaySkills(tempArray);
        var inputField = document.getElementById("skillsearch") as HTMLInputElement;
        inputField.value == "";
    }

    function addObs() {
        // console.log(processedSkills)
;       const typeIds = new Set(['71', '95', '97', '98', '99', '101', '102', '103', '104', '105']);
        const isFilteredType = (element) => typeIds.has(element.id);
        //const result = processedSkills.find( ({id}) => id === '71');
        const result = processedSkills.filter(isFilteredType);
        // console.log(result);
        // Spread operator (...) Required to make this work!
        setDisplaySkills([...result]);
    }

    function processSkills(skillsdata) {
        var tempArray = [];
        for (const [key, value] of Object.entries(skillsdata)) {
            //console.log('Key is ' + key + ' and value is ' + value);
            tempArray.push({
                id: key,
                value: value,
                name: 'skill[' + key + ']'
             });
        }
        //console.log(tempArray);
        return tempArray;
    }

    useEffect(() => {
        console.log('query is ' + query);
    }, [query])

    useEffect(() => {
        // Prepare the JSON array so it can be searched and provide
        // identifiers when returned to server
        setProcessedSkills(processSkills(skillz));
        if(typeof selskillz != 'undefined' && processSkills(selskillz).length > 0) {
            console.log("Selected skillz");
            setDisplaySkills(processSkills(selskillz));
        }
    }, []);

    return(
            <><div className="flex">
            <input id="skillsearch" name="skillsearch" className="shadow appearance-none border rounded w-10/12 py-2 px-3 leading-tight mr-6" type="search" placeholder="Enter a search term here" onChange={event => setQuery(event.target.value)} onBlur={event => { event.target.value = ""; } } />
            <button className="shadow appearance-none bg-green-600 hover:bg-green-700 border-green-600 hover:border-red-700 leading-tight border text-white py-2 px-3 rounded w-2/12" type="button" onClick={() => addObs()}>
                Add Obs
            </button>
        </div><AuditSkillsOptionsComponent query={query} skills={processedSkills} addSkillClick={addSkill} displayC={displaySkills} /><AuditSelectedSkillsComponent displayC={displaySkills} delSkill={delSkill} /></>
    );
}
export default AuditSkillComponent;

const AuditSelectedSkillsComponent: React.FC<any> = ({displayC, delSkill}) => {

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
                    <button className="shadow appearance-none bg-red-600 hover:bg-red-700 border-red-600 hover:border-red-700 leading-tight border text-white py-2 px-3 rounded w-2/12" type="button" onClick={event => delSkill(event.target.parentNode.getElementsByTagName("input")[0].id)}>
                        Remove
                    </button>
                </div>
            );
    });

    return(tempArray);

}

type AuditSkillsOptionsComponentProps = {
    query: string,
    skills: [],
    addCompClick: any,
    displayC: any,
}

type skillsObjectProps = {
    id: string,
    value: string,
    name: string,
}

const AuditSkillsOptionsComponent: React.FC<AuditSkillsOptionsComponentProps> = ({query, skills, addSkillClick, displayC}) => {
    const [selectedSkills, setSelectedSkills] = useState([]);

    function clickedSkill(result) {
        setSelectedSkills([]);
        addSkillClick(result);
    }

    useEffect(() => {
        if(query.length > 2) {
            var tempResults = [];
            const tempArray = [];
            // Need to remove competencies that have already been selected
            const arrayNoAlreadySelected = [].concat(
                skills.filter((obj1: skillsObjectProps) => displayC.every((obj2: skillsObjectProps)  => obj1.id !== obj2.id)),
                displayC.filter((obj2: skillsObjectProps) => skills.every((obj1: skillsObjectProps) => obj2.id !== obj1.id))
            );
            console.log(arrayNoAlreadySelected);
            tempResults = arrayNoAlreadySelected.filter((item: SkillListProps) => {
                return item.value.toLowerCase().indexOf(query.toLowerCase()) > -1;
            });
            tempResults.forEach((result) => tempArray.push(
                <li key={result.id} name={result.name} className="py-1 px-1 big-text" onClick={() => clickedSkill(result)}>
                    {result.value}
                </li>
            ));
            setSelectedSkills(tempArray);
        } else {
            setSelectedSkills(undefined);
        }
    }, [query]);

    return(
        <>
        {
            typeof selectedSkills !== 'undefined' && selectedSkills.length > 0 ?
            <div>
                <ul className="bg-purple-100 px-2 py-2 shadow-lg border-2 rounded mt-2 absolute z-50 mr-2 border-purple-700">
                    {selectedSkills}
                </ul>
            </div>
            : null
        }
        </>
    )
}

if (document.getElementById('audit_skill_component')) {
    const element = document.getElementById('audit_skill_component');
    // create new props object with element's data-attributes
    const props = Object.assign({}, element.dataset);
    ReactDOM.render(<AuditSkillComponent {...props}/>,
        document.getElementById('audit_skill_component'));
}
