import { useState, useEffect } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

const BookList = ({ onEdit }) => {
    const [books, setBooks] = useState([]);
    const [isLoading, setIsLoading] = useState(true);

    useEffect(() => {
        fetchBooks();
    }, []);


    
    const fetchBooks = () => {
        apiFetch({ path: '/library/v1/books' })
            .then(data => {
                setBooks(data);
                setIsLoading(false);
            })
            .catch(error => console.error(error));
    };

    const deleteBook = (id) => {
        if (!confirm('Are you sure you want to delete this book?')) return;

        apiFetch({ 
            path: `/library/v1/books/${id}`,
            method: 'DELETE'
        }).then(() => {
            fetchBooks(); 
        });
    };

    if (isLoading) return <p>Loading books...</p>;

    return (
        <table className="library-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Year</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                {books.length === 0 ? (
                    <tr><td colSpan="5">No books found.</td></tr>
                ) : (
                    books.map(book => (
                        <tr key={book.id}>
                            <td>{book.title}</td>
                            <td>{book.author}</td>
                            <td>{book.publication_year}</td>
                            <td>
                                <span className={`status-${book.status}`}>{book.status}</span>
                            </td>
                            <td>
                                <button className="button button-small" onClick={() => onEdit(book)}>Edit</button>
                                <button className="button button-small button-link-delete" onClick={() => deleteBook(book.id)} style={{marginLeft: '10px', color: '#a00'}}>Delete</button>
                            </td>
                        </tr>
                    ))
                )}
            </tbody>
        </table>
    );
};

export default BookList;