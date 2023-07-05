import React, {Component} from 'react';
import { connect } from "react-redux";
import OfferCard from './OfferCard';
import OffersSelector from './OffersSelector';

class OffersSet extends Component
{
    static mapStateToProps(state, props) {
        return props;
    };

    render() 
    {
        return (
            <div className="flex flex-col space-y-4">
                {this.props.offersData.map(offerData => (
                    <OfferCard offerData={offerData}
                               offersSetRelation={this.props.offersSetRelation}
                               contextTemporaryID={this.props.contextId} 
                               columnTemporaryId={this.props.columnId}/>
                ))}
                <OffersSelector 
                    offersSetRelation={this.props.offersSetRelation}
                    offers={this.props.offersData} 
                    contextTemporaryID={this.props.contextId} 
                    columnTemporaryId={this.props.columnId}/>
                />
            </div>
        );
    }
}

export default connect(OffersSet.mapStateToProps, {})(OffersSet);