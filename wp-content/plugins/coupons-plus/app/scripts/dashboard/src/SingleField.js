import Field from './Field';

export default class SingleField
{
    field = {};

    constructor(field: object) 
    {
        this.field = field;
    }

    render() 
    {
        return (<Field {...this.field}/>)
    }
}