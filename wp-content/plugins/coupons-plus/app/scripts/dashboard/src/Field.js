import React, {Component} from 'react';
import { connect } from "react-redux";
import Select from './fields/Select';
import Input from './fields/Input';
import Multiple from './fields/multiple';
import Switch from './fields/Switch';
import RepeaterFields from './RepeaterFields';

class Field extends Component
{
    static fields = {
        select: Select,
        input: Input,
        multiple: Multiple,
        switch: Switch,
        repeater: RepeaterFields
    };

    static mapStateToProps(state, props) {
        return props;
    };

    render() 
    {
        const FieldComponent = Field.fields[this.props.type]
        if (this.props.show && !this.props.show()) {
            return '';
        }
        return <FieldComponent {...this.props}/>;
    }
}

export default connect(Field.mapStateToProps, {})(Field);