import SingleField from './SingleField';
import classNames from 'classnames';

export default class FieldsGroup
{
    fields = [];

    constructor(fields: array, options: object) 
    {
        this.fields = fields;
        this.options = options;
        this.direction = options && options.direction === 'vertical'? 'vertical' : 'horizontal'
    }

    render() 
    {
        if (this.options && typeof this.options.show === 'function' && !this.options.show())  {
            return '';
        }

        return (
            <div className={classNames({
                'flex flex-row': true,
                'flex-row space-x-3': this.direction === 'horizontal',
                'items-end': this.direction === 'horizontal' && (this.options? !this.options.customAlignmentClass : true),
                'flex-col': this.direction === 'vertical',
                'space-y-3': this.direction === 'vertical' && !this.options?.narrow,
                'space-y-1': this.direction === 'vertical' && this.options?.narrow,
                'items-start': this.direction === 'vertical' && (this.options? !this.options.customAlignmentClass : true),
                [this.options?.customAlignmentClass || '']: this.options?.customAlignmentClass
            })}>
                {this.fields.filter(field => field).map(field => {
                    if (field instanceof FieldsGroup) {
                        return field;
                    }
                    return new SingleField(field);
                }).map(field => field.render())}
            </div>
        )
    }
}