import React from 'react';
import ReactDOM from 'react-dom/client';
import './index.css';

class Node extends React.Component {
  render() {
    return (
      <button className="node" onClick={() => this.props.onClick()} >
        {'--'.repeat(this.props.data.level)} {this.props.data.name} id={this.props.data.id}
      </button>
    );
  }
}

class App extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      status: 'Loading...',
      tree: [],
      selectedNode: []
    };
  }

  componentDidMount() {
    fetch(apiHost + 'props')
      .then((response) => response.json())
      .then((data) => this.setState({ status: '', tree: data }))
      .catch((error) => this.handleFetchError(error));
  }

  handleFetchError(error) {
    console.error('Error: ', error);
    this.setState({ status: 'Failed to load' });
  }

  processTree(tree, l, i) {
    let content = [];
    for (let node of tree) {
      let nodeComp = this.renderNode({id: node.id, name: node.name, level: l});
      content.push(<div key={i} className="tree-row">{ nodeComp }</div>);
      if (node.children.length > 0) {
        let children = this.processTree(node.children, l + 1, i + 1);
        content.push(...children);
      }
      i = content.length + 1;
    }

    if (content.length === 0) {
      return <div>No data</div>;
    }

    return content;
  }

  processNode(nodeData) {
    let content = [];
    let i = 0;
    for (let row of nodeData) {
      content.push(<div key={'n' + i} className="tree-row">{ row.property } { row.relation ? '(' + row.relation + ')' : '' }</div>);
      ++i;
    }

    if (content.length === 0) {
      return '';
    }

    return content;
  }

  handleClick(d) {
    this.setState({ status: 'Loading...' });
    fetch(apiHost + 'prop/' + d.id)
      .then((response) => response.json())
      .then((data) => this.setState({ status: 'Selected: ' + d.name, selectedNode: data }))
      .catch((error) => this.handleFetchError(error));
  }

  renderNode(d) {
    return <Node data={d} onClick={() => this.handleClick(d)} />;
  }

  render() {
    return (
      <div className="rep-app">
        <h1>Real Estate Properties</h1>
        <div className="tree">
          <div className="tree-view">
            <div className="status">{this.state.status}</div>
            { this.processTree(this.state.tree, 0, 0) }
          </div>
          <div className="tree-node-info">
            { this.processNode(this.state.selectedNode) }
          </div>
        </div>
      </div>
    );
  }
}

// ========================================

const apiHost = 'http://localhost:8080/';
const root = ReactDOM.createRoot(document.getElementById("root"));

root.render(<App />);
